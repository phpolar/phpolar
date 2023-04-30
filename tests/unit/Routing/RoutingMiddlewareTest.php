<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(RoutingMiddleware::class)]
final class RoutingMiddlewareTest extends TestCase
{
    #[TestDox("Shall process the routing request handler")]
    public function test1()
    {
        /**
         * @var MockObject&RoutingHandler $routingHandlerSpy
         */
        $routingHandlerSpy = $this->createMock(RoutingHandler::class);
        $routingHandlerSpy->expects($this->once())->method("handle");
        $sut = new RoutingMiddleware($routingHandlerSpy, new StreamFactoryStub("r"));
        $noopHandler = $this->createStub(RequestHandlerInterface::class);
        $sut->process(new RequestStub(), $noopHandler);
    }

    #[TestDox("Shall process the next handler if the route is NOT matched")]
    public function test2()
    {
        /**
         * @var MockObject&RequestHandlerInterface $nextHandlerSpy
         */
        $nextHandlerSpy = $this->createMock(RequestHandlerInterface::class);
        $nextHandlerSpy->expects($this->once())->method("handle");
        /**
         * @var Stub&RoutingHandler $routingHandlerStub
         */
        $routingHandlerStub = $this->createStub(RoutingHandler::class);
        $matchingRouteResponse = new ResponseStub(ResponseCode::NOT_FOUND);
        $routingHandlerStub->method("handle")->willReturn($matchingRouteResponse);
        $sut = new RoutingMiddleware($routingHandlerStub, new StreamFactoryStub("r"));
        $sut->process(new RequestStub(), $nextHandlerSpy);
    }

    #[TestDox("Shall NOT process the next handler if the route is matched")]
    public function test3()
    {
        /**
         * @var MockObject&RequestHandlerInterface $nextHandlerSpy
         */
        $nextHandlerSpy = $this->createMock(RequestHandlerInterface::class);
        $nextHandlerSpy->expects($this->never())->method("handle");
        /**
         * @var Stub&RoutingHandler $routingHandlerStub
         */
        $routingHandlerStub = $this->createStub(RoutingHandler::class);
        $routingHandlerStub->method("handle")->willReturn(
            new ResponseStub(ResponseCode::OK)
        );
        $sut = new RoutingMiddleware($routingHandlerStub, new StreamFactoryStub("r"));
        $response = $sut->process(new RequestStub(), $nextHandlerSpy);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
