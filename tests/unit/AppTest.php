<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Closure;
use DateTimeImmutable;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use PhpContrib\Http\Message\ResponseFilterInterface;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Http\CsrfProtectionRequestHandler;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\Phpolar\Auth\AbstractProtectedRoutable;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Phpolar\DependencyInjection\ContainerLoader;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\Http\PathVariableBindings;
use Phpolar\Phpolar\Http\Representations;
use Phpolar\Phpolar\Http\RequestProcessingHandler;
use Phpolar\Phpolar\Http\RequestProcessorExecutor;
use Phpolar\Phpolar\Http\Server;
use Phpolar\Phpolar\Http\ServerInterface;
use Phpolar\Phpolar\Http\Target;
use Phpolar\Phpolar\Http\ResponseCodeResolver;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[RunTestsInSeparateProcesses]
#[CoversClass(App::class)]
#[UsesClass(Server::class)]
#[UsesClass(ContainerLoader::class)]
#[UsesClass(MiddlewareQueueRequestHandler::class)]
#[UsesClass(RequestProcessingHandler::class)]
#[UsesClass(RoutingMiddleware::class)]
#[UsesClass(ProtectedRoutableResolver::class)]
#[UsesClass(AuthorizationChecker::class)]
#[UsesClass(PathVariableBindings::class)]
#[UsesClass(Representations::class)]
#[UsesClass(RequestProcessorExecutor::class)]
#[UsesClass(Target::class)]
final class AppTest extends TestCase
{
    public const RESPONSE_CONTENT = "it worked!";
    const RESPONSE_STATUS = 500;
    const HEADER_KEY = "Content-Range";
    const HEADER_VALUE = "bytes 21010-47021/47022";
    const ERROR_HANDLER_404 = "ERROR_HANDLER_404";

    protected function getContainerFactory(
        ArrayAccess $config,
        MiddlewareQueueRequestHandler|Closure $handler,
        CsrfRequestCheckMiddleware|Closure|null $csrfPreRoutingMiddleware = null,
        CsrfResponseFilterMiddleware|Closure|null $csrfPostRoutingMiddleware = null,
    ): ContainerInterface {
        $config[TemplateEngine::class] = static fn() => new TemplateEngine();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub((new StreamFactoryStub("+w"))->createStream());
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[MiddlewareQueueRequestHandler::class] = $handler;
        $config[DiTokens::RESPONSE_EMITTER] = new SapiEmitter();
        $config[self::ERROR_HANDLER_404] = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new ResponseStub(HttpResponseCodeEnum::NotFound->value);
            }
        };
        $config[DiTokens::CSRF_CHECK_MIDDLEWARE] = $csrfPreRoutingMiddleware;
        $config[DiTokens::CSRF_RESPONSE_FILTER_MIDDLEWARE] = $csrfPostRoutingMiddleware;
        $config[AbstractTokenStorage::class] = $this->createStub(AbstractTokenStorage::class);
        $config[ResponseFilterInterface::class] = $this->createStub(ResponseFilterInterface::class);
        $config[AuthenticatorInterface::class] = $this->createStub(AuthenticatorInterface::class);
        $config[PropertyInjectorInterface::class] = $this->createStub(PropertyInjectorInterface::class);
        $config[ResponseCodeResolver::class] = new ResponseCodeResolver();

        return new ConfigurableContainerStub($config);
    }

    private function getNonConfiguredContainer(ArrayAccess $config): ContainerInterface
    {
        $container = new ConfigurableContainerStub($config);
        $config[ContainerInterface::class] = $container;
        return $container;
    }

    private function configureContainer(ContainerInterface $container, ArrayAccess $containerConfig): ContainerInterface
    {
        (new ContainerLoader())->load($container, $containerConfig);
        return $container;
    }

    #[TestDox("Shall delegate request processing to the routing middleware")]
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub("+w");
        $request = new RequestStub();
        $routingMiddlewareSpy = $this->createMock(MiddlewareInterface::class);
        $routingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(HttpResponseCodeEnum::Ok->value)
                    ->withBody($streamFactory->createStream())
            );
        $config = new ContainerConfigurationStub();
        $config[ServerInterface::class] = $this->createStub(ServerInterface::class);
        $config[RoutingMiddleware::class] = $routingMiddlewareSpy;
        $config[CsrfProtectionRequestHandler::class] = static fn(ArrayAccess $config) =>
        new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $config[AbstractTokenStorage::class],
            $config[ResponseFactoryInterface::class],
            "",
        );
        $handler = static fn(ArrayAccess $config) => new MiddlewareQueueRequestHandler($config[self::ERROR_HANDLER_404]);
        $containerFac = $this->getContainerFactory($config, $handler);
        // do not use the container config file
        chdir(__DIR__);
        $server = App::create(
            $this->configureContainer($containerFac, $config),
        );
        $server->receive($request);
        $this->assertSame(HttpResponseCodeEnum::Ok->value, http_response_code());
    }

    #[TestDox("Shall allow for configuring the server to use CSRF middleware (2)")]
    public function test2()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub("+w");
        $request = new RequestStub();
        $csrfPreRoutingMiddleware = static fn(ArrayAccess $config) => new class ($config[CsrfProtectionRequestHandler::class]) extends CsrfRequestCheckMiddleware {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };
        $csrfPostRoutingMiddleware = static fn(ArrayAccess $config) =>
        new class (
            $config[ResponseFilterInterface::class],
        ) extends CsrfResponseFilterMiddleware {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $response = $handler->handle($request);
                // do something with it...
                return $response;
            }
        };
        $routingMiddlewareSpy = $this->createMock(MiddlewareInterface::class);
        $routingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(HttpResponseCodeEnum::Ok->value)
                    ->withBody($streamFactory->createStream())
            );
        $config = new ContainerConfigurationStub();
        $config[RoutingMiddleware::class] = $routingMiddlewareSpy;
        $config[ServerInterface::class] = $this->createStub(ServerInterface::class);
        $config[CsrfProtectionRequestHandler::class] = static fn(ArrayAccess $config) =>
        new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $config[AbstractTokenStorage::class],
            $config[ResponseFactoryInterface::class],
            "",
            "",
        );
        $handler = static fn(ArrayAccess $config) => new MiddlewareQueueRequestHandler($config[self::ERROR_HANDLER_404]);
        $containerFac = $this->getContainerFactory($config, $handler, $csrfPreRoutingMiddleware, $csrfPostRoutingMiddleware);
        // do not use the container config file
        chdir(__DIR__);
        $server = App::create(
            $this->configureContainer($containerFac, $config),
        );
        $server->useCsrfMiddleware();
        $server->receive($request);
        $this->assertSame(HttpResponseCodeEnum::Ok->value, http_response_code());
    }

    #[TestDox("Shall add custom services to the provided dependency injection container")]
    public function test4()
    {
        $config = new ContainerConfigurationStub();
        $config[PropertyInjectorInterface::class] = $this->createStub(PropertyInjectorInterface::class);
        $config[ResponseCodeResolver::class] = new ResponseCodeResolver();
        $nonConfiguredContainerFac = $this->getNonConfiguredContainer($config);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $requestStub
            ->method("getMethod")
            ->willReturn(HttpMethodEnum::Get->value);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeTypeEnum::TextHtml->value]);
        chdir("tests/__fakes__/");
        $app = App::create(
            $this->configureContainer($nonConfiguredContainerFac, $config),
        );
        $app->receive($requestStub);
        $this->assertSame(HttpResponseCodeEnum::NotFound->value, http_response_code());
    }

    #[TestDox("Shall be a singleton object")]
    public function test6()
    {
        $config = new ContainerConfigurationStub();
        $config[TemplatingStrategyInterface::class] = $this->createStub(TemplatingStrategyInterface::class);
        $config[StreamFactoryInterface::class] = $this->createStub(StreamFactoryInterface::class);
        $config[ResponseFactoryInterface::class] = $this->createStub(ResponseFactoryInterface::class);
        $config[PropertyInjectorInterface::class] = $this->createStub(PropertyInjectorInterface::class);
        $config[ResponseCodeResolver::class] = new ResponseCodeResolver();
        $containerFac = $this->getNonConfiguredContainer($config);
        chdir("tests/__fakes__/");
        $app1 = App::create(
            $this->configureContainer($containerFac, $config),
        );
        $app2 = App::create(
            $this->configureContainer($containerFac, $config),
        );
        $this->assertSame($app1, $app2);
    }

    #[TestDox("Shall support opt-in authorization")]
    public function test7()
    {
        $handler = static fn(ArrayAccess $config) => new MiddlewareQueueRequestHandler($config[self::ERROR_HANDLER_404]);
        $config = new ContainerConfigurationStub();
        $config[ModelResolverInterface::class] = $this->createStub(ModelResolverInterface::class);
        $config[DiTokens::UNAUTHORIZED_HANDLER] = $this->createStub(RequestHandlerInterface::class);
        $routable = $this->createStub(AbstractProtectedRoutable::class);
        $routable
            ->method("process")
            ->willReturn("");
        $config[ServerInterface::class] = new Server(
            interface: [
                new Target(
                    location: "/",
                    method: HttpMethodEnum::Get,
                    representations: new Representations([MimeTypeEnum::TextHtml]),
                    requestProcessor: $routable,
                ),
            ],
        );
        $container = $this->configureContainer($this->getContainerFactory($config, $handler), $config);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $requestStub
            ->method("getMethod")
            ->willReturn(HttpMethodEnum::Get->value);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeTypeEnum::TextHtml->value]);
        $sut = App::create($container);
        $sut->useAuthorization();
        $sut->receive($requestStub);
        $this->assertSame(HttpResponseCodeEnum::Ok->value, http_response_code());
    }

    #[TestDox("Shall support queueing any PSR-15 middleware")]
    public function test8()
    {
        $handler = static fn(ArrayAccess $config) => new MiddlewareQueueRequestHandler($config[self::ERROR_HANDLER_404]);
        $config = new ContainerConfigurationStub();
        $config[ModelResolverInterface::class] = $this->createStub(ModelResolverInterface::class);
        $config[DiTokens::UNAUTHORIZED_HANDLER] = $this->createStub(RequestHandlerInterface::class);
        $config[ServerInterface::class] = new Server(
            interface: [
                new Target(
                    location: "/",
                    method: HttpMethodEnum::Get,
                    representations: new Representations([]),
                    requestProcessor: $this->createStub(AbstractProtectedRoutable::class),
                ),
            ],
        );
        $config[RoutingMiddleware::class] = $this->createStub(MiddlewareInterface::class);
        $container = $this->configureContainer($this->getContainerFactory($config, $handler), $config);
        $givenMiddleware = $this->createStub(MiddlewareInterface::class);
        $expectedResponse = new ResponseStub(HttpResponseCodeEnum::ImATeapot->value);
        $givenMiddleware->method("process")->willReturn(
            $expectedResponse->withBody((new StreamFactoryStub("+w"))->createStream())
        );
        $sut = App::create($container);
        $sut->use($givenMiddleware);
        $sut->receive(new RequestStub());
        $this->assertSame(HttpResponseCodeEnum::ImATeapot->value, http_response_code());
    }
}
