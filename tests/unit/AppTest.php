<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Closure;
use Phpolar\CsrfProtection\CsrfTokenGenerator;
use Phpolar\CsrfProtection\Http\CsrfProtectionRequestHandler;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Http\ResponseFilterStrategyInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\DependencyInjection\ClosureContainerFactory;
use Phpolar\Phpolar\DependencyInjection\ContainerManager;
use Phpolar\Phpolar\Routing\AbstractContentDelegate;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\PrimaryHandler;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[RunTestsInSeparateProcesses]
#[CoversClass(App::class)]
#[CoversClass(ContainerManager::class)]
#[UsesClass(RouteRegistry::class)]
final class AppTest extends TestCase
{
    public const RESPONSE_CONTENT = "it worked!";
    const RESPONSE_STATUS = 500;
    const HEADER_KEY = "Content-Range";
    const HEADER_VALUE = "bytes 21010-47021/47022";

    protected function getContainerFactory(
        ArrayAccess $config,
        PrimaryHandler|Closure $handler,
        CsrfRequestCheckMiddleware|Closure|null $csrfPreRoutingMiddleware = null,
        CsrfResponseFilterMiddleware|Closure|null $csrfPostRoutingMiddleware = null,
    ): ClosureContainerFactory {
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[TemplateEngine::class] = static fn (ArrayAccess $config) => new TemplateEngine($config[TemplatingStrategyInterface::class], $config[Binder::class], $config[Dispatcher::class]);
        $config[Binder::class] = new Binder();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[Dispatcher::class] = new Dispatcher();
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[PrimaryHandler::class] = $handler;
        $config[App::ERROR_HANDLER_404] = static fn (ArrayAccess $config) => new ErrorHandler(ResponseCode::NOT_FOUND, "Not Found", $config[ContainerInterface::class]);
        $config[CsrfRequestCheckMiddleware::class] = $csrfPreRoutingMiddleware;
        $config[CsrfResponseFilterMiddleware::class] = $csrfPostRoutingMiddleware;
        $config[CsrfTokenGenerator::class] = $this->createStub(CsrfTokenGenerator::class);
        $config[AbstractTokenStorage::class] = $this->createStub(AbstractTokenStorage::class);
        $config[ResponseFilterStrategyInterface::class] = $this->createStub(ResponseFilterStrategyInterface::class);

        $containerFac = static fn (ArrayAccess $container): ContainerInterface =>
        new ConfigurableContainerStub($container);

        return new class ($containerFac) extends ClosureContainerFactory {
        };
    }

    private function getNonConfiguredContainer(): ClosureContainerFactory
    {
        $containerFac = static function (ArrayAccess $config): ContainerInterface {
            $container = new ConfigurableContainerStub($config);
            $config[ContainerInterface::class] = $container;
            return $container;
        };
        return new class ($containerFac) extends ClosureContainerFactory {
        };
    }

    #[TestDox("Shall delegate request processing to the routing middleware")]
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        /**
         * @var MockObject&RoutingMiddleware $routingMiddlewareSpy
         */
        $routingMiddlewareSpy = $this->createMock(RoutingMiddleware::class);
        $routingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(ResponseCode::OK)
                    ->withBody($streamFactory->createStream())
            );
        $config = new ContainerConfigurationStub();
        $routes = new RouteRegistry();
        $config[RouteRegistry::class] = $routes;
        $config[RoutingMiddleware::class] = $routingMiddlewareSpy;
        $config[CsrfProtectionRequestHandler::class] = static fn (ArrayAccess $config) =>
            new CsrfProtectionRequestHandler(
                $config[ResponseFactoryInterface::class],
                $config[AbstractTokenStorage::class],
                "",
                "",
            );
        $handler = static fn (ArrayAccess $config) => new PrimaryHandler($config[App::ERROR_HANDLER_404]);
        $containerFac = $this->getContainerFactory($config, $handler);
        // do not use the container config file
        chdir(__DIR__);
        $server = App::create(new ContainerManager($containerFac, $config));
        $server->useRoutes($routes);
        $server->receive($request);
        $this->assertSame(ResponseCode::OK, http_response_code());
    }

    #[TestDox("Shall allow for configuring the server to use CSRF middleware (2)")]
    public function test2()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $csrfPreRoutingMiddleware = static fn (ArrayAccess $config) => new class ($config[CsrfProtectionRequestHandler::class]) extends CsrfRequestCheckMiddleware {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };
        /**
         * @var MockObject&CsrfResponseFilterMiddleware $csrfPostRoutingMiddlewareSpy
         */
        $csrfPostRoutingMiddleware = static fn (ArrayAccess $config) =>
            new class (
                $config[AbstractTokenStorage::class],
                $config[CsrfTokenGenerator::class],
                $config[ResponseFilterStrategyInterface::class],
            ) extends CsrfResponseFilterMiddleware {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    $response = $handler->handle($request);
                    // do something with it...
                    return $response;
                }
            };
        /**
         * @var MockObject&RoutingMiddleware $routingMiddlewareSpy
         */
        $routingMiddlewareSpy = $this->createMock(RoutingMiddleware::class);
        $routingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(ResponseCode::OK)
                    ->withBody($streamFactory->createStream())
            );
        $config = new ContainerConfigurationStub();
        $routes = new RouteRegistry();
        $config[RouteRegistry::class] = $routes;
        $config[RoutingMiddleware::class] = $routingMiddlewareSpy;
        $config[CsrfProtectionRequestHandler::class] = static fn (ArrayAccess $config) =>
            new CsrfProtectionRequestHandler(
                $config[ResponseFactoryInterface::class],
                $config[AbstractTokenStorage::class],
                "",
                "",
            );
        $handler = static fn (ArrayAccess $config) => new PrimaryHandler($config[App::ERROR_HANDLER_404]);
        $containerFac = $this->getContainerFactory($config, $handler, $csrfPreRoutingMiddleware, $csrfPostRoutingMiddleware);
        // do not use the container config file
        chdir(__DIR__);
        $server = App::create(new ContainerManager($containerFac, $config));
        $server->useCsrfMiddleware();
        $server->useRoutes($routes);
        $server->receive($request);
        $this->assertSame(ResponseCode::OK, http_response_code());
    }

    #[TestDox("Shall add given routes to default route handler")]
    public function test3()
    {
        $expectedContent = "EXPECTED CONTENT";
        $givenRoutes = new RouteRegistry();
        /**
         * @var Stub&AbstractContentDelegate $handlerStub
         */
        $handlerStub = $this->createStub(AbstractContentDelegate::class);
        $handlerStub->method("getResponseContent")->willReturn($expectedContent);
        $givenRoutes->add("GET", "/", $handlerStub);
        $givenRequest = new RequestStub("GET", "/");
        $handlerStub = $this->createStub(PrimaryHandler::class);
        $config = new ContainerConfigurationStub();
        $containerFac = $this->getContainerFactory($config, $handlerStub);
        $container = $containerFac->getContainer($config);
        $sut = App::create(new ContainerManager($containerFac, $config));
        $sut->useRoutes($givenRoutes);
        /**
         * @var RouteRegistry $configuredRoutes
         */
        $configuredRoutes = $config[RouteRegistry::class];
        $configuredHandler = $configuredRoutes->match($givenRequest);
        $this->assertSame($expectedContent, $configuredHandler->getResponseContent($container));
    }

    #[TestDox("Shall add custom services to the provided dependency injection container")]
    public function test4()
    {
        $config = new ContainerConfigurationStub();
        $config[TemplatingStrategyInterface::class] = $this->createStub(TemplatingStrategyInterface::class);
        $config[StreamFactoryInterface::class] = $this->createStub(StreamFactoryInterface::class);
        $config[ResponseFactoryInterface::class] = $this->createStub(ResponseFactoryInterface::class);
        $nonConfiguredContainerFac = $this->getNonConfiguredContainer();
        chdir("tests/__fakes__/");
        $app = App::create(new ContainerManager($nonConfiguredContainerFac, $config));
        $app->receive(new RequestStub());
        $this->expectOutputString("<h1>Not Found</h1>");
    }

    #[TestDox("Shall process the 404 error handler if the request path does not exist")]
    public function test5()
    {
        $this->expectOutputString("<h1>Not Found</h1>");
        $config = new ContainerConfigurationStub();
        /**
         * @var Stub&PrimaryHandler $handlerStub
         */
        $handlerStub = $this->createStub(PrimaryHandler::class);
        $handlerStub->method("handle")->willReturn((new ResponseStub(404, "Not Found")));
        $container = $this->getContainerFactory($config, $handlerStub);
        $sut = App::create(new ContainerManager($container, $config));
        $sut->receive(new RequestStub("GET", "/non-existing-route"));
        $this->assertSame(ResponseCode::NOT_FOUND, http_response_code());
    }

    #[TestDox("Shall be a singleton object")]
    public function test6()
    {
        $config = new ContainerConfigurationStub();
        $config[TemplatingStrategyInterface::class] = $this->createStub(TemplatingStrategyInterface::class);
        $config[StreamFactoryInterface::class] = $this->createStub(StreamFactoryInterface::class);
        $config[ResponseFactoryInterface::class] = $this->createStub(ResponseFactoryInterface::class);
        $nonConfiguredContainerFac = $this->getNonConfiguredContainer();
        chdir("tests/__fakes__/");
        $app1 = App::create(new ContainerManager($nonConfiguredContainerFac, $config));
        $app2 = App::create(new ContainerManager($nonConfiguredContainerFac, $config));
        $this->assertSame($app1, $app2);
    }
}