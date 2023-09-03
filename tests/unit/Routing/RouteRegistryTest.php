<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteRegistry::class)]
final class RouteRegistryTest extends TestCase
{
    #[TestDox("Shall retrieve the request handlers associated with a route")]
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

    #[TestDox("Shall return a RouteNotRegistered instance when a route is not associated with any handlers.")]
    public function test2()
    {
        $sut = new RouteRegistry();
        $result = $sut->get("an_unregistered_route");
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }
}
