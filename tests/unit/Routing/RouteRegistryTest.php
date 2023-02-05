<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Routing\RouteRegistry
 */
final class RoutingRegistryTest extends TestCase
{
    /**
     * @testdox Shall retrieve the request handlers associated with a route
     */
    public function test1()
    {
        $givenRoute = "/";
        /**
         * @var MockObject&AbstractRequestHandler $handlerSpy
         */
        $handlerSpy = $this->getMockForAbstractClass(AbstractRequestHandler::class);
        $handlerSpy->expects($this->once())->method("handle")->willReturn("");
        $sut = new RouteRegistry();
        $sut->add($givenRoute, $handlerSpy);
        $registeredHandler = $sut->get($givenRoute);
        $registeredHandler->handle();
    }

    /**
     * @testdox Shall return a RouteNotRegistered instance when a route is not associated with any handlers.
     */
    public function test2()
    {
        $sut = new RouteRegistry();
        $result = $sut->get("an_unregistered_route");
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }
}
