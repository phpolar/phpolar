<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Generator;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\HttpMessageTestUtils\UriStub;
use Phpolar\Model\Model;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Auth\AbstractProtectedRoutable;
use Phpolar\Phpolar\Auth\Authenticate;
use Phpolar\Phpolar\Auth\AuthenticatorInterface;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\RoutableInterface;
use Phpolar\Phpolar\RoutableResolverInterface;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(RoutingHandler::class)]
#[CoversClass(RouteRegistry::class)]
#[UsesClass(ErrorHandler::class)]
#[UsesClass(ResolvedRoute::class)]
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
                    return $this->streamFactory ?? new StreamFactoryStub("w");
                }
                if ($id === TemplateEngine::class) {
                    return new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
                }
                if ($id === DiTokens::UNAUTHORIZED_HANDLER) {
                    return new class () implements RequestHandlerInterface {
                        public function handle(ServerRequestInterface $request): ResponseInterface
                        {
                            return (new ResponseStub())->withBody((new StreamFactoryStub("w"))->createStream("BANG!"));
                        }
                    };
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
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $this->createStub(RoutableResolverInterface::class),
            $this->createStub(RequestHandlerInterface::class),
        );
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
        $registeredRouteHandler = $this->createMock(RoutableInterface::class);
        $registeredRouteHandler->expects($this->once())->method("process");
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        /**
         * @var Stub&RoutableResolverInterface
         */
        $routableResolver = $this->createStub(RoutableResolverInterface::class);
        $routableResolver->method("resolve")->willReturn($registeredRouteHandler);
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $routableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
    }

    #[TestDox("Shall attempt to authenticate the registered route handler for \$requestMethod requests")]
    #[DataProvider("requestMethods")]
    public function test2b(string $requestMethod)
    {
        /**
         * @var Stub&RoutableInterface $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createStub(AbstractProtectedRoutable::class);
        /**
         * @var MockObject&RoutableResolverInterface
         */
        $protectedRoutableResolver = $this->createMock(RoutableResolverInterface::class);
        $protectedRoutableResolver->expects($this->once())->method("resolve")->willReturn($registeredRouteHandler);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $protectedRoutableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
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
        $registeredRouteHandler = $this->createStub(RoutableInterface::class);
        $registeredRouteHandler->method("process")->willReturn($responseContent);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        /**
         * @var Stub&RoutableResolverInterface
         */
        $routableResolver = $this->createStub(RoutableResolverInterface::class);
        $routableResolver->method("resolve")->willReturn($registeredRouteHandler);
        $container = $this->getContainer($streamFactoryStub);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $routableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
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
        $registeredRouteHandler = new class () implements RoutableInterface {
            public function process(ContainerInterface $container, string $id = ""): string
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
        /**
         * @var Stub&RoutableResolverInterface
         */
        $routableResolver = $this->createStub(RoutableResolverInterface::class);
        $routableResolver->method("resolve")->willReturn($registeredRouteHandler);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $routableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame($givenIdRouteParam, $response->getBody()->getContents());
    }

    #[TestDox("Shall pass the resolved route params to the handler and attempt to authenticate when the names match")]
    public function testa2()
    {
        $givenIdRouteParam = "123";
        $responseContent = $givenIdRouteParam;
        /**
         * @var MockObject&StreamFactoryStub
         */
        $streamFactoryStub = $this->createMock(StreamFactoryStub::class);
        $streamFactoryStub->expects($this->once())->method("createStream")->with($responseContent)->willReturn(new MemoryStreamStub($responseContent));
        $container = $this->getContainer($streamFactoryStub);
        $registeredRouteHandler = new class () extends AbstractProtectedRoutable {
            public function process(ContainerInterface $container, string $id = ""): string
            {
                return $id;
            }
        };
        /**
         * @var MockObject&RoutableResolverInterface
         */
        $protectedRoutableResolver = $this->createMock(RoutableResolverInterface::class);
        $protectedRoutableResolver->expects($this->once())->method("resolve")->willReturn($registeredRouteHandler);
        $routeParamMap = new RouteParamMap("/some/path/{id}", "/some/path/$givenIdRouteParam");
        $resolvedRoute = new ResolvedRoute($registeredRouteHandler, $routeParamMap);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($resolvedRoute);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $protectedRoutableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
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
        $registeredRouteHandler = new class () implements RoutableInterface {
            public function process(ContainerInterface $container, #[Model] object $form = null): string
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
        /**
         * @var Stub&RoutableResolverInterface
         */
        $routableResolver = $this->createStub(RoutableResolverInterface::class);
        $routableResolver->method("resolve")->willReturn($registeredRouteHandler);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactoryMock,
            $container,
            $modelResolverMock,
            $routableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
        $sut->handle($request);
    }

    #[TestDox("Shall pass model parameters to the authenticated handler")]
    public function teste()
    {
        $expectedModelName = uniqid();
        $fakeModel = (object) ["name" => $expectedModelName];
        $container = $this->getContainer();
        $registeredRouteHandler = new class () extends AbstractProtectedRoutable {
            #[Authenticate]
            public function process(ContainerInterface $container, #[Model] object $form = null): string
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
         * @var Stub&RouteRegistry
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        /**
         * @var Stub&RoutableResolverInterface
         */
        $routableResolver = $this->createStub(RoutableResolverInterface::class);
        $routableResolver->method("resolve")->willReturn($registeredRouteHandler);
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolverMock,
            $routableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
        $response = $sut->handle($request);
        $this->assertSame($fakeModel->name, $response->getBody()->getContents());
    }

    #[TestDox("Should set the user property of the authenticated routable")]
    #[DataProvider("requestMethods")]
    public function testf(string $requestMethod)
    {
        $fakeUserName = "FAKE_NAME";
        $registeredRouteHandler = new class () extends AbstractProtectedRoutable {
            #[Authenticate]
            public function process(ContainerInterface $container): string
            {
                return $this->user->name;
            }
        };

        /**
         * @var MockObject&AuthenticatorInterface
         */
        $authenticatorStub = $this->createStub(AuthenticatorInterface::class);
        $authenticatorStub->method("getCredentials")->willReturn((object) [
            "user" => (object) [
                "name" => $fakeUserName,
                "nickname" => "FAKE_NICKNAME",
                "email" => "fake@fake.fake",
                "avatarUrl" => "https://fake.fake/fake",
            ],
        ]);
        $protectedRoutableResolver = new ProtectedRoutableResolver($authenticatorStub);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $protectedRoutableResolver,
            $this->createStub(RequestHandlerInterface::class),
        );
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame($fakeUserName, $response->getBody()->getContents());
    }

    #[TestDox("Should call configured unauthorized handler")]
    #[DataProvider("requestMethods")]
    public function testg(string $requestMethod)
    {
        $registeredRouteHandler = new class () extends AbstractProtectedRoutable {
            #[Authenticate]
            public function process(ContainerInterface $container): string
            {
                return "";
            }
        };

        /**
         * @var MockObject&AuthenticatorInterface
         */
        $authenticatorStub = $this->createStub(AuthenticatorInterface::class);
        $authenticatorStub->method("getCredentials")->willReturn(null);
        $protectedRoutableResolver = new ProtectedRoutableResolver($authenticatorStub);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("match")->willReturn($registeredRouteHandler);
        $container = $this->getContainer();
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $modelResolver = $this->createStub(ModelResolverInterface::class);
        $unauthHandler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseStub())->withBody((new StreamFactoryStub("w"))->createStream("BANG!"));
            }
        };
        $sut = new RoutingHandler(
            $routeRegistryStub,
            $responseFactory,
            $streamFactory,
            $container,
            $modelResolver,
            $protectedRoutableResolver,
            $unauthHandler,
        );
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame("BANG!", $response->getBody()->getContents());
    }
}
