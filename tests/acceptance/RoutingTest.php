<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Exception;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\MemoryStreamStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\UriStub;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class RoutingTest extends TestCase
{
    protected function getResponseFactory(): ResponseFactoryInterface
    {
        return new class() implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new ResponseStub($code, $reasonPhrase);
            }
        };
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        return new class() implements StreamFactoryInterface {
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

    #[Test]
    #[TestDox("Shall invoke the handler registered to the given route")]
    public function criterion_1()
    {
        $givenRoute = "/";
        $expectedResponse = "<h1>Found!</h1>";
        $routeRegistry = new RouteRegistry();
        $indexHandler = new class($expectedResponse) extends AbstractRequestHandler {
            public function __construct(private string $responseTemplate)
            {
            }

            public function handle(): string
            {
                return $this->responseTemplate;
            }
        };
        $responseFactory = $this->getResponseFactory();
        $streamFactory = $this->getStreamFactory();
        $routeRegistry->add($givenRoute, $indexHandler);
        $routingHandler = new DefaultRoutingHandler(
            responseFactory: $responseFactory,
            streamFactory: $streamFactory,
            routeRegistry: $routeRegistry,
        );
        $requestStub = (new RequestStub("GET"))->withUri(new UriStub($givenRoute));
        $response = $routingHandler->handle($requestStub);
        $this->assertSame($expectedResponse, $response->getBody()->getContents());
    }

    #[Test]
    #[TestDox("Shall return a \"not found\" response when the given route has not been registered")]
    public function criterion_2()
    {
        $givenRoute = "an_unregistered_route";
        $expectedStatusCode = ResponseCode::NOT_FOUND;
        $routeRegistry = new RouteRegistry();
        $responseFactory = $this->getResponseFactory();
        $streamFactory = $this->getStreamFactory();
        $routingHandler = new DefaultRoutingHandler(
            responseFactory: $responseFactory,
            streamFactory: $streamFactory,
            routeRegistry: $routeRegistry,
        );
        $requestStub = (new RequestStub("GET"))->withUri(new UriStub($givenRoute));
        $response = $routingHandler->handle($requestStub);
        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertSame("Not Found", $response->getReasonPhrase());
    }
}
