<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddleware;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Tests\Stubs\ContainerStub;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\StreamContentStrategy;
use Phpolar\PhpTemplating\TemplateEngine;
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
#[CoversClass(WebServer::class)]
#[CoversClass(MiddlewareProcessingQueue::class)]
#[UsesClass(RouteRegistry::class)]
final class WebServerTest extends TestCase
{
    const RESPONSE_CONTENT = "it worked!";
    const RESPONSE_STATUS = 500;
    const HEADER_KEY = "Content-Range";
    const HEADER_VALUE = "bytes 21010-47021/47022";

    protected function getContainer(
        RequestHandlerInterface $handler,
        ?CsrfPreRoutingMiddleware $csrfPreRoutingMiddleware = null,
        ?CsrfPostRoutingMiddlewareFactory $csrfPostRoutingMiddlewareFactory = null,
    ): ContainerInterface {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $templateEngine = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $errorHandler = new Error401Handler($responseFactory, $streamFactory, $templateEngine);
        $middlewareQueue = new MiddlewareProcessingQueue();
        return new ContainerStub(
            $responseFactory,
            $streamFactory,
            $handler,
            $errorHandler,
            $templateEngine,
            $middlewareQueue,
            $csrfPreRoutingMiddleware,
            $csrfPostRoutingMiddlewareFactory
        );
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
        $server = WebServer::createApp($this->getContainer($handler));
        $server->receive($request);
        $this->assertTrue($handler->wasUsed);
    }

    #[TestDox("Shall set the HTTP response code")]
    public function test2()
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
                    ->withStatus(WebServerTest::RESPONSE_STATUS);
            }
        };
        $server = WebServer::createApp($this->getContainer($handler));
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
        $server = WebServer::createApp($this->getContainer($handler));
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
        $server = WebServer::createApp($this->getContainer($handler, $csrfPreRoutingMiddlewareSpy, null));
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
        $server = WebServer::createApp($this->getContainer($handler, $csrfPreRoutingMiddlewareSpy, $csrfPostRoutingMiddlewareSpyFactoryStub));
        $server->useCsrfMiddleware();
        $server->receive($request);
        $this->assertSame(ResponseCode::OK, http_response_code());
    }
}
