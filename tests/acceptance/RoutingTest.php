<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Exception;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\UriStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

#[TestDox("HTTP Request Routing")]
final class RoutingTest extends TestCase
{
    protected function getContainer(): ContainerInterface
    {
        return new class ($this->getResponseFactory(), $this->getStreamFactory()) implements ContainerInterface {
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactory
            ) {
            }
            public function has(string $id): bool
            {
                return true;
            }

            public function get(string $id)
            {
                if ($id === ResponseFactoryInterface::class) {
                    return $this->responseFactory;
                }

                if ($id === StreamFactoryInterface::class) {
                    return $this->streamFactory;
                }
                if ($id === TemplateEngine::class) {
                    return new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
                }
            }
        };
    }

    protected function getResponseFactory(): ResponseFactoryInterface
    {
        return new class () implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new ResponseStub($code, $reasonPhrase);
            }
        };
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        return new class () implements StreamFactoryInterface {
            public function createStream(string $content = ''): StreamInterface
            {
                return new MemoryStreamStub($content);
            }
            public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
            {
                throw new Exception("not implemented");
            }
            public function createStreamFromResource($resource): StreamInterface
            {
                throw new Exception("not implemented");
            }
        };
    }

    protected function getModelResolver(): ModelResolverInterface
    {
        return $this->createStub(ModelResolverInterface::class);
    }

    #[Test]
    #[TestDox("Shall invoke the routable object registered to the given request path")]
    public function criterion1()
    {
        $givenRoute = "/";
        $expectedResponse = "<h1>Found!</h1>";
        $routeRegistry = new RouteRegistry();
        $indexHandler = new class ($expectedResponse) implements RoutableInterface {
            public function __construct(private string $responseTemplate)
            {
            }

            public function process(ContainerInterface $container): string
            {
                return $this->responseTemplate;
            }
        };
        $container = $this->getContainer();
        $routeRegistry->add("GET", $givenRoute, $indexHandler);
        $routingHandler = new RoutingHandler(
            routeRegistry: $routeRegistry,
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            errorHandler: new ErrorHandler(0, "", $container),
            container: $container,
            modelResolver: $this->getModelResolver(),
        );
        $requestStub = (new RequestStub("GET"))->withUri(new UriStub($givenRoute));
        $response = $routingHandler->handle($requestStub);
        $this->assertSame($expectedResponse, $response->getBody()->getContents());
    }

    #[Test]
    #[TestDox("Shall return a \"not found\" response when the given route has not been registered")]
    public function criterion2()
    {
        $givenRoute = "an_unregistered_route";
        $expectedStatusCode = ResponseCode::NOT_FOUND;
        $routeRegistry = new RouteRegistry();
        $container = $this->getContainer();
        $routingHandler = new RoutingHandler(
            routeRegistry: $routeRegistry,
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            errorHandler: new ErrorHandler(404, "Not Found", $container),
            container: $container,
            modelResolver: $this->getModelResolver(),
        );
        $requestStub = (new RequestStub("GET"))->withUri(new UriStub($givenRoute));
        $response = $routingHandler->handle($requestStub);
        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertSame("Not Found", $response->getReasonPhrase());
        $this->assertSame("<h1>Not Found</h1>", $response->getBody()->getContents());
    }
}
