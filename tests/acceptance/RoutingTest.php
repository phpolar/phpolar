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
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[TestDox("HTTP Request Routing")]
final class RoutingTest extends TestCase
{
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
        $propertyInjector = $this->createStub(PropertyInjectorInterface::class);
        $routeRegistry = new RouteMap($propertyInjector);
        $indexHandler = new class ($expectedResponse) implements RoutableInterface {
            public function __construct(private string $responseTemplate)
            {
            }

            public function process(): string
            {
                return $this->responseTemplate;
            }
        };
        $routeRegistry->add(RequestMethods::GET, $givenRoute, $indexHandler);
        $routingHandler = new RoutingHandler(
            routeRegistry: $routeRegistry,
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            modelResolver: $this->getModelResolver(),
            authChecker: new AuthorizationChecker(
                routableResolver: new class () implements RoutableResolverInterface {
                    public function resolve(RoutableInterface $target): RoutableInterface|false
                    {
                        return $target;
                    }
                },
                unauthHandler: $this->createStub(RequestHandlerInterface::class),
            ),
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
        $propertyInjector = $this->createStub(PropertyInjectorInterface::class);
        $routeRegistry = new RouteMap($propertyInjector);
        $routingHandler = new RoutingHandler(
            routeRegistry: $routeRegistry,
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            modelResolver: $this->getModelResolver(),
            authChecker: new AuthorizationChecker(
                routableResolver: new class () implements RoutableResolverInterface {
                    public function resolve(RoutableInterface $target): RoutableInterface|false
                    {
                        return $target;
                    }
                },
                unauthHandler: $this->createStub(RequestHandlerInterface::class),
            ),
        );
        $requestStub = (new RequestStub("GET"))->withUri(new UriStub($givenRoute));
        $response = $routingHandler->handle($requestStub);
        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertSame("Not Found", $response->getReasonPhrase());
    }
}
