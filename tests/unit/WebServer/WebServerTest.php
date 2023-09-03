<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Closure;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddleware;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Routing\AbstractRouteDelegate;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\StreamContentStrategy;
use Phpolar\PhpTemplating\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
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
use ReflectionObject;

#[RunTestsInSeparateProcesses]
#[CoversClass(WebServer::class)]
#[CoversClass(MiddlewareProcessingQueue::class)]
#[CoversClass(ContainerManager::class)]
#[UsesClass(RouteRegistry::class)]
final class WebServerTest extends TestCase
{
    public const RESPONSE_CONTENT = "it worked!";
    const RESPONSE_STATUS = 500;
    const HEADER_KEY = "Content-Range";
    const HEADER_VALUE = "bytes 21010-47021/47022";

    protected function getContainerFactory(
        ArrayAccess $config,
        RequestHandlerInterface $handler,
        ?CsrfPreRoutingMiddleware $csrfPreRoutingMiddleware = null,
        ?CsrfPostRoutingMiddlewareFactory $csrfPostRoutingMiddlewareFactory = null,
    ): AbstractContainerFactory {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[Binder::class] = new Binder();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[Dispatcher::class] = new Dispatcher();
        $config[WebServer::PRIMARY_REQUEST_HANDLER] = $handler;
        $config[ResponseFactoryInterface::class] = $responseFactory;
        $config[StreamFactoryInterface::class] = $streamFactory;
        $config[CsrfPreRoutingMiddleware::class] = $csrfPreRoutingMiddleware ?? new CsrfPreRoutingMiddleware($responseFactory, $streamFactory);
        $config[CsrfPostRoutingMiddlewareFactory::class] = $csrfPostRoutingMiddlewareFactory ?? new CsrfPostRoutingMiddlewareFactory($responseFactory, $streamFactory);

        $containerFac = static fn (ArrayAccess $container): ContainerInterface =>
        new ConfigurableContainerStub($container);

        return new class ($containerFac) extends AbstractContainerFactory {
        };
    }

    private function getNonConfiguredContainer(): AbstractContainerFactory
    {
        $containerFac = static function (ArrayAccess $config): ContainerInterface {
            $container = new ConfigurableContainerStub($config);
            $config[ContainerInterface::class] = $container;
            return $container;
        };
        return new class ($containerFac) extends AbstractContainerFactory {
        };
    }

    #[TestDox("Shall use the given routing handler to handle requests")]
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $handler = new class ($responseFactory, $streamFactory) implements RequestHandlerInterface {
            public bool $wasUsed = false;
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory,
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->wasUsed = true;
                return $this->responseFactory->createResponse()->withBody(
                    $this->streamFactory->createStream()
                );
            }
        };
        $config = new ContainerConfigurationStub();
        $container = $this->getContainerFactory($config, $handler);
        $server = WebServer::createApp($container, $config);
        $server->receive($request);
        $this->assertTrue($handler->wasUsed);
    }

    #[TestDox("Shall set the HTTP response code")]
    public function test2()
    {
        $this->expectOutputRegex("/Internal Server Error/");
        $responseFactory = new ResponseFactoryStub();
        ;
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $handler = new class ($responseFactory, $streamFactory) implements RequestHandlerInterface {
            public bool $wasUsed = false;
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory,
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->wasUsed = true;
                return $this->responseFactory->createResponse()
                    ->withBody($this->streamFactory->createStream())
                    ->withStatus(WebServerTest::RESPONSE_STATUS, "Internal Server Error");
            }
        };
        $config = new ContainerConfigurationStub();
        $server = WebServer::createApp($this->getContainerFactory($config, $handler), $config);
        $server->receive($request);
        $this->assertSame(WebServerTest::RESPONSE_STATUS, http_response_code());
    }

    #[TestDox("Shall set the HTTP headers")]
    public function test3()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $handler = new class ($responseFactory, $streamFactory) implements RequestHandlerInterface {
            public bool $wasUsed = false;
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory,
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->wasUsed = true;
                return $this->responseFactory->createResponse()
                    ->withBody($this->streamFactory->createStream())
                    ->withHeader(WebServerTest::HEADER_KEY, WebServerTest::HEADER_VALUE);
            }
        };
        $config = new ContainerConfigurationStub();
        $server = WebServer::createApp($this->getContainerFactory($config, $handler), $config);
        $server->receive($request);
        $this->assertContains(
            sprintf("%s: %s", WebServerTest::HEADER_KEY, WebServerTest::HEADER_VALUE),
            \xdebug_get_headers()
        );
    }

    #[TestDox("Shall allow for configuring the server to use CSRF middleware (2)")]
    public function test4()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $handler = new class ($responseFactory, $streamFactory) implements RequestHandlerInterface {
            public bool $wasUsed = false;
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory,
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->wasUsed = true;
                return $this->responseFactory->createResponse()
                    ->withBody($this->streamFactory->createStream())
                    ->withHeader(WebServerTest::HEADER_KEY, WebServerTest::HEADER_VALUE);
            }
        };
        /**
         * @var MockObject&CsrfPreRoutingMiddleware $csrfPreRoutingMiddlewareSpy
         */
        $csrfPreRoutingMiddlewareSpy = $this->createMock(CsrfPreRoutingMiddleware::class);
        $csrfPreRoutingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(ResponseCode::BAD_REQUEST)
                    ->withBody($streamFactory->createStream())
            );
        $config = new ContainerConfigurationStub();
        $server = WebServer::createApp($this->getContainerFactory($config, $handler, $csrfPreRoutingMiddlewareSpy, null), $config);
        $server->useCsrfMiddleware();
        $server->receive($request);
        $this->assertSame(ResponseCode::BAD_REQUEST, http_response_code());
    }


    #[TestDox("Shall allow for configuring the server to use CSRF middleware (2)")]
    public function test5()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $request = new RequestStub();
        $handler = new class ($responseFactory, $streamFactory) implements RequestHandlerInterface {
            public bool $wasUsed = false;
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory,
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->wasUsed = true;
                return $this->responseFactory->createResponse()
                    ->withBody($this->streamFactory->createStream())
                    ->withHeader(WebServerTest::HEADER_KEY, WebServerTest::HEADER_VALUE);
            }
        };
        /**
         * @var MockObject&CsrfPreRoutingMiddleware $csrfPreRoutingMiddlewareSpy
         */
        $csrfPreRoutingMiddlewareSpy = $this->createMock(CsrfPreRoutingMiddleware::class);
        $csrfPreRoutingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(ResponseCode::OK)
                    ->withBody($streamFactory->createStream(self::RESPONSE_CONTENT))
            );
        /**
         * @var MockObject&CsrfPostRoutingMiddleware $csrfPostRoutingMiddlewareSpy
         */
        $csrfPostRoutingMiddlewareSpy = $this->createMock(CsrfPostRoutingMiddleware::class);
        $csrfPostRoutingMiddlewareSpy
            ->expects($this->once())
            ->method("process")
            ->willReturn(
                $responseFactory->createResponse(ResponseCode::OK)
                    ->withBody($streamFactory->createStream())
            );

        /**
         * @var Stub&CsrfPostRoutingMiddlewareFactory $csrfPostRoutingMiddlewareSpyFactoryStub
         */
        $csrfPostRoutingMiddlewareSpyFactoryStub = $this->createStub(CsrfPostRoutingMiddlewareFactory::class);
        $csrfPostRoutingMiddlewareSpyFactoryStub->method("getMiddleware")->willReturn($csrfPostRoutingMiddlewareSpy);
        $config = new ContainerConfigurationStub();
        $server = WebServer::createApp($this->getContainerFactory($config, $handler, $csrfPreRoutingMiddlewareSpy, $csrfPostRoutingMiddlewareSpyFactoryStub), $config);
        $server->useCsrfMiddleware();
        $server->receive($request);
        $this->assertSame(ResponseCode::OK, http_response_code());
    }

    #[TestDox("Shall add given routes to default route handler")]
    public function test6a()
    {
        $givenRoutes = new RouteRegistry();
        $handlerStub = $this->createStub(RequestHandlerInterface::class);
        $config = new ContainerConfigurationStub();
        $container = $this->getContainerFactory($config, $handlerStub);
        $sut = WebServer::createApp($container, $config);
        $reflectionObj = new ReflectionObject($sut);
        $useRoutesProp = $reflectionObj->getProperty("shouldUseRoutes");
        $useRoutesProp->setAccessible(true);
        $routesProp = $reflectionObj->getProperty("routes");
        $routesProp->setAccessible(true);
        $this->assertFalse($useRoutesProp->getValue($sut));
        $sut->useRoutes($givenRoutes);
        $this->assertTrue($useRoutesProp->getValue($sut));
        $this->assertEquals($givenRoutes, $routesProp->getValue($sut));
    }

    #[TestDox("Shall respond to POST requests")]
    public function test6b()
    {
        $expectedResponse = "<h1>Responding to POST request!</h1>";
        $this->expectOutputString($expectedResponse);
        $givenPath = "/some-path";
        $request = new RequestStub("POST", $givenPath);
        $givenRoutes = new RouteRegistry();
        $givenRoutes->addPost(
            $givenPath,
            new class ($expectedResponse) extends AbstractRouteDelegate {
                public function __construct(private string $expectedResponse)
                {
                }
                public function handle(ContainerInterface $container): string
                {
                    return $this->expectedResponse;
                }
            },
        );
        $config = new ContainerConfigurationStub();
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $containerFac = new ContainerFactory(static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf));
        $sut = WebServer::createApp($containerFac, $config);
        $sut->useRoutes($givenRoutes);
        $sut->receive($request);
        $this->assertSame(ResponseCode::OK, http_response_code());
    }

    #[TestDox("Shall add required services to the provided dependency injection container")]
    public function test7()
    {
        $nonConfiguredContainer = $this->getNonConfiguredContainer();
        WebServer::createApp($nonConfiguredContainer, new ContainerConfigurationStub());
        $this->expectNotToPerformAssertions();
    }

    #[TestDox("Shall add custom services to the provided dependency injection container")]
    public function test8()
    {
        $nonConfiguredContainerFac = $this->getNonConfiguredContainer();
        chdir("tests/__fakes__/");
        $app = WebServer::createApp($nonConfiguredContainerFac, new ContainerConfigurationStub());
        $app->receive(new RequestStub());
        $this->expectOutputString(WebServerTest::RESPONSE_CONTENT);
    }

    #[TestDox("Shall process the 404 error handler if the request path does not exist")]
    public function test9()
    {
        $this->expectOutputString("<h1>Not Found</h1>");
        $config = new ContainerConfigurationStub();
        /**
         * @var Stub&RequestHandlerInterface $handlerStub
         */
        $handlerStub = $this->createStub(RequestHandlerInterface::class);
        $handlerStub->method("handle")->willReturn((new ResponseStub(404, "Not Found")));
        $container = $this->getContainerFactory($config, $handlerStub);
        $sut = WebServer::createApp($container, $config);
        $sut->receive(new RequestStub("GET", "/non-existing-route"));
        $this->assertSame(ResponseCode::NOT_FOUND, http_response_code());
    }
}
