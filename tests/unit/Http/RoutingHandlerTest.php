<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Generator;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\HttpMessageTestUtils\UriStub;
use Phpolar\Model\Model;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Phpolar\Http\ErrorHandler;
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
                    return $this->streamFactory ?? new StreamFactoryStub("r");
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
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container, $modelResolver);
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
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container, $modelResolver);
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
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container, $modelResolver);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
        $this->assertSame($responseContent, $response->getBody()->getContents());
    }

    #[TestDox("Shall pass the resolved route params to the handler when the names match")]
    public function testa()
    {
        $givenIdRouteParam = "123";
        $responseContent = $givenIdRouteParam;
        /**
         * @var MockObject&StreamFactoryStub
         */
        $streamFactoryStub = $this->createMock(StreamFactoryStub::class);
        $streamFactoryStub->expects($this->once())->method("createStream")->with($responseContent)->willReturn(new MemoryStreamStub($responseContent));
        $container = $this->getContainer($streamFactoryStub);
        $registeredRouteHandler = new class () extends AbstractContentDelegate {
            public function getResponseContent(ContainerInterface $container, string $id = ""): string
            {
                return $id;
            }
        };
        $routeParamMap = new RouteParamMap("/some/path/{id}", "/some/path/$givenIdRouteParam");
        $resolvedRoute = new ResolvedRoute($registeredRouteHandler, $routeParamMap);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($resolvedRoute);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler($routeRegistryStub, $responseFactory, $streamFactory, $errorHandler, $container, $modelResolver);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame($givenIdRouteParam, $response->getBody()->getContents());
    }

    #[TestDox("Shall pass model parameters to the routable handler")]
    public function testc()
    {
        $expectedModelName = uniqid();
        $fakeModel = (object) ["name" => $expectedModelName];
        $container = $this->getContainer();
        $registeredRouteHandler = new class () extends AbstractContentDelegate {
            public function getResponseContent(ContainerInterface $container, #[Model] object $form = null): string
            {
                return $form->name;
            }
        };
        /**
         * @var MockObject&ModelResolverInterface
         */
        $modelResolverMock = $this->createMock(ModelResolverInterface::class);
        $modelResolverMock->method("resolve")->willReturn(["form" => $fakeModel]);
        /**
         * @var MockObject&StreamFactoryInterface
         */
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $streamFactoryMock->expects($this->once())->method("createStream")->with($fakeModel->name);
        /**
         * @var Stub&RouteRegistry
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $errorHandler = new ErrorHandler(404, "Not Found", $container);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactoryMock,
            $errorHandler,
            $container,
            $modelResolverMock,
        );
        $sut->handle($request);
    }
}
