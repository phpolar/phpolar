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

#[CoversClass(DefaultRoutingHandler::class)]
#[CoversClass(RouteRegistry::class)]
final class DefaultRoutingHandlerTest extends TestCase
{
    #[TestDox("Shall respond with \"Not Found\" if the route is not registered")]
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("get")->willReturn(new RouteNotRegistered());
        $sut = new DefaultRoutingHandler($responseFactory, $streamFactory, $routeRegistryStub);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::NOT_FOUND, $response->getStatusCode());
    }

    #[TestDox("Shall call the registered route handler")]
    public function test2()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        /**
         * @var MockObject $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createMock(AbstractRequestHandler::class);
        $registeredRouteHandler->expects($this->once())->method("handle");
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("get")->willReturn($registeredRouteHandler);
        $sut = new DefaultRoutingHandler($responseFactory, $streamFactory, $routeRegistryStub);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
    }

    #[TestDox("Shall create the response stream")]
    public function test3()
    {
        $responseContent = uniqid();
        $responseFactory = new ResponseFactoryStub();
        /**
         * @var MockObject&StreamFactoryStub
         */
        $streamFactoryStub = $this->createMock(StreamFactoryStub::class);
        $streamFactoryStub->expects($this->once())->method("createStream")->with($responseContent)->willReturn(new MemoryStreamStub($responseContent));
        /**
         * @var Stub $registeredRouteHandler
         */
        $registeredRouteHandler = $this->createStub(AbstractRequestHandler::class);
        $registeredRouteHandler->method("handle")->willReturn($responseContent);
        /**
         * @var Stub&RouteRegistry $routeRegistryStub
         */
        $routeRegistryStub = $this->createStub(RouteRegistry::class);
        $routeRegistryStub->method("get")->willReturn($registeredRouteHandler);
        $sut = new DefaultRoutingHandler($responseFactory, $streamFactoryStub, $routeRegistryStub);
        $request = (new RequestStub())->withUri(new UriStub(uniqid()));
        $response = $sut->handle($request);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
        $this->assertSame($responseContent, $response->getBody()->getContents());
    }
}
