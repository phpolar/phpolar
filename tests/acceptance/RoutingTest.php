<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Exception;
use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[TestDox("HTTP Request Routing")]
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
    #[TestDox("Shall invoke the routable object registered to the given request path")]
    public function criterion1()
    {
        $givenRoute = "/";
        $expectedResponse = "<h1>Found!</h1>";
        $propertyInjector = $this->createStub(PropertyInjectorInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $requestStub
            ->method("getMethod")
            ->willReturn(HttpMethod::Get->value);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $uriStub
            ->method("getPath")
            ->willReturn($givenRoute);

        $streamStub
            ->method("getContents")
            ->willReturn($expectedResponse);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);

        $indexHandler = new class($expectedResponse) implements RequestProcessorInterface {
            public function __construct(private string $responseTemplate) {}

            public function process(): string
            {
                return $this->responseTemplate;
            }
        };

        $requestProcessor = new RequestProcessingHandler(
            server: new Server([
                new Target(
                    location: $givenRoute,
                    method: HttpMethod::Get,
                    representations: new Representations([
                        MimeType::TextHtml,
                    ]),
                    requestProcessor: $indexHandler,
                ),
            ]),
            processorExecutor: new RequestProcessorExecutor(),
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            requestAuthorizer: new RequestAuthorizer(
                routableResolver: new class() implements RequestProcessorResolverInterface {
                    public function resolve(RequestProcessorInterface $target): RequestProcessorInterface|false
                    {
                        return $target;
                    }
                },
                unauthHandler: $this->createStub(RequestHandlerInterface::class),
            ),
            propertyInjector: $propertyInjector,
            modelResolver: $modelResolver,
            responseCodeResolver: new ResponseCodeResolver(),
        );

        $response = $requestProcessor->handle($requestStub);

        $this->assertSame(HttpResponseCode::Ok->value, $response->getStatusCode());
        $this->assertSame($expectedResponse, $response->getBody()->getContents());
    }

    #[Test]
    #[TestDox("Shall return a \"not found\" response when the given location has not been registered")]
    public function criterion2()
    {
        $givenLocation = "AN_UNREGISTERED_ROUTE";
        $registeredLocation = "A_REGISTERED_ROUTE";

        $uriStub = $this->createStub(UriInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub
            ->method("getMethod")
            ->willReturn(HttpMethod::Get->value);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $uriStub
            ->method("getPath")
            ->willReturn($givenLocation);

        $propertyInjector = $this->createStub(PropertyInjectorInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $indexHandler = new class() implements RequestProcessorInterface {
            public function __construct() {}

            public function process(): string
            {
                // intentionally returning an empty string
                return "";
            }
        };

        $requestProcessor = new RequestProcessingHandler(
            processorExecutor: new RequestProcessorExecutor(),
            server: new Server([
                new Target(
                    location: $registeredLocation,
                    method: HttpMethod::Get,
                    representations: new Representations([
                        MimeType::TextHtml,
                    ]),
                    requestProcessor: $indexHandler,
                ),
            ]),
            responseFactory: $this->getResponseFactory(),
            streamFactory: $this->getStreamFactory(),
            requestAuthorizer: new RequestAuthorizer(
                routableResolver: new class() implements RequestProcessorResolverInterface {
                    public function resolve(RequestProcessorInterface $target): RequestProcessorInterface|false
                    {
                        return $target;
                    }
                },
                unauthHandler: $this->createStub(RequestHandlerInterface::class),
            ),
            propertyInjector: $propertyInjector,
            modelResolver: $modelResolver,
            responseCodeResolver: new ResponseCodeResolver(),
        );

        $response = $requestProcessor->handle($requestStub);

        $this->assertSame(HttpResponseCode::NotFound->value, $response->getStatusCode());
        $this->assertSame(HttpResponseCode::NotFound->getLabel(), $response->getReasonPhrase());
    }
}
