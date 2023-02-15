<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\MemoryStreamStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\UriStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(DefaultRoutingHandler::class)]
#[CoversClass(RouteRegistry::class)]
final class DefaultRoutingHandlerTest extends TestCase
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
            }
        };
    }

    #[TestDox("Shall respond with \"Not Found\" if the route is not registered")]
    public function test1()
    {
        $container = $this->getContainer();
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("fromGet")->willReturn(new RouteNotRegistered());
        $sut = new DefaultRoutingHandler($routeRegistryStub, $container);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::NOT_FOUND, $response->getStatusCode());
    }

    #[TestDox("Shall call the registered route handler")]
    public function test2()
    {
        $container = $this->getContainer();
        /**
         * @var MockObject $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createMock(AbstractRouteDelegate::class);
        $registeredRouteHandler->expects($this->once())->method("handle");
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("fromGet")->willReturn($registeredRouteHandler);
        $sut = new DefaultRoutingHandler($routeRegistryStub, $container);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
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
        $container = $this->getContainer($streamFactoryStub);
        /**
         * @var Stub $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createStub(AbstractRouteDelegate::class);
        $registeredRouteHandler->method("handle")->willReturn($responseContent);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("fromGet")->willReturn($registeredRouteHandler);
        $sut = new DefaultRoutingHandler($routeRegistryStub, $container);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
        $this->assertSame($responseContent, $response->getBody()->getContents());
    }
}
