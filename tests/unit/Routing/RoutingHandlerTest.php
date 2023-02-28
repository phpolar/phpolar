<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Generator;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\MemoryStreamStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\UriStub;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(RoutingHandler::class)]
#[CoversClass(RouteRegistry::class)]
final class RoutingHandlerTest extends TestCase
{
    public function getContainer(?StreamFactoryInterface $streamFactory = null): ContainerInterface
    {
        return new class ($streamFactory) implements ContainerInterface {
            public function __construct(private ?StreamFactoryInterface $streamFactory)
            {
            }
            public function has(string $id): bool
            {
                return true;
            }
            public function get(string $id)
            {
                if ($id === ResponseFactoryInterface::class) {
                    return new ResponseFactoryStub();
                }
                if ($id === StreamFactoryInterface::class) {
                    return $this->streamFactory ?? new StreamFactoryStub();
                }
                if ($id === TemplateEngine::class) {
                    return new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
                }
            }
        };
    }

    public static function requestMethods(): Generator
    {
        yield ["GET"];
        yield ["POST"];
    }

    #[TestDox("Shall respond with \"Not Found\" if the route is not registered for \$requestMethod requests")]
    #[DataProvider("requestMethods")]
    public function test1(string $requestMethod)
    {
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn(new RouteNotRegistered());
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container);
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::NOT_FOUND, $response->getStatusCode());
    }

    #[TestDox("Shall call the registered route handler for \$requestMethod requests")]
    #[DataProvider("requestMethods")]
    public function test2(string $requestMethod)
    {
        /**
         * @var MockObject $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createMock(AbstractContentDelegate::class);
        $registeredRouteHandler->expects($this->once())->method("getResponseContent");
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container);
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
    }

    #[TestDox("Shall create the response stream")]
    public function test3()
    {
        $responseContent = uniqid();
        /**
         * @var MockObject&StreamFactoryStub
         */
        $streamFactoryStub = $this->createMock(StreamFactoryStub::class);
        $streamFactoryStub->expects($this->once())->method("createStream")->with($responseContent)->willReturn(new MemoryStreamStub($responseContent));
        /**
         * @var Stub $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createStub(AbstractContentDelegate::class);
        $registeredRouteHandler->method("getResponseContent")->willReturn($responseContent);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $container = $this->getContainer($streamFactoryStub);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
        $this->assertSame($responseContent, $response->getBody()->getContents());
    }
}
