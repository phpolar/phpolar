<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(RoutingMiddleware::class)]
final class RoutingMiddlewareTest extends TestCase
{
    #[TestDox("Shall process the routing request handler")]
    public function test1()
    {
        $routingHandlerSpy = $this->createMock(RequestHandlerInterface::class);
        $routingHandlerSpy->expects($this->once())->method("handle");
        $sut = new RoutingMiddleware($routingHandlerSpy, new StreamFactoryStub("r"));
        $noopHandler = $this->createStub(RequestHandlerInterface::class);
        $sut->process(new RequestStub(), $noopHandler);
    }

    #[TestDox("Shall process the next handler if the route is NOT matched")]
    public function test2()
    {
        $nextHandlerSpy = $this->createMock(RequestHandlerInterface::class);
        $nextHandlerSpy->expects($this->once())->method("handle");
        $routingHandlerStub = $this->createStub(RequestHandlerInterface::class);
        $matchingRouteResponse = new ResponseStub(ResponseCode::NOT_FOUND);
        $routingHandlerStub->method("handle")->willReturn($matchingRouteResponse);
        $sut = new RoutingMiddleware($routingHandlerStub, new StreamFactoryStub("r"));
        $sut->process(new RequestStub(), $nextHandlerSpy);
    }

    #[TestDox("Shall NOT process the next handler if the route is matched")]
    public function test3()
    {
        $nextHandlerSpy = $this->createMock(RequestHandlerInterface::class);
        $nextHandlerSpy->expects($this->never())->method("handle");
        $routingHandlerStub = $this->createStub(RequestHandlerInterface::class);
        $routingHandlerStub->method("handle")->willReturn(
            new ResponseStub(ResponseCode::OK)
        );
        $sut = new RoutingMiddleware($routingHandlerStub, new StreamFactoryStub("r"));
        $response = $sut->process(new RequestStub(), $nextHandlerSpy);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
