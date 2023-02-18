<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteRegistry::class)]
final class RouteRegistryTest extends TestCase
{
    #[TestDox("Shall retrieve the request handlers associated with a GET request")]
    public function test1()
    {
        $givenRoute = "/";
        /**
         * @var MockObject&AbstractRouteDelegate $handlerSpy
         */
        $handlerSpy = $this->getMockForAbstractClass(AbstractRouteDelegate::class);
        $handlerSpy->expects($this->once())->method("handle")->willReturn("");
        $sut = new RouteRegistry();
        $sut->addGet($givenRoute, $handlerSpy);
        $registeredHandler = $sut->fromGet($givenRoute);
        $registeredHandler->handle(new ConfigurableContainerStub(new ContainerConfigurationStub()));
    }

    #[TestDox("Shall retrieve the request handlers associated with a POST request")]
    public function test2()
    {
        $givenRoute = "/";
        /**
         * @var MockObject&AbstractRouteDelegate $handlerSpy
         */
        $handlerSpy = $this->getMockForAbstractClass(AbstractRouteDelegate::class);
        $handlerSpy->expects($this->once())->method("handle")->willReturn("");
        $sut = new RouteRegistry();
        $sut->addPost($givenRoute, $handlerSpy);
        $registeredHandler = $sut->fromPost($givenRoute);
        $registeredHandler->handle(new ConfigurableContainerStub(new ContainerConfigurationStub()));
    }

    #[TestDox("Shall return a RouteNotRegistered instance when a route is not associated with any handlers.")]
    public function test3()
    {
        $sut = new RouteRegistry();
        $result1 = $sut->fromGet("an_unregistered_route");
        $result2 = $sut->fromPost("an_unregistered_route");
        $this->assertInstanceOf(RouteNotRegistered::class, $result1);
        $this->assertInstanceOf(RouteNotRegistered::class, $result2);
    }

    #[TestDox("Shall not register a \"GET\" request in the \"POST\" request registry.")]
    public function test4()
    {
        $givenRoute = "/";
        /**
         * @var Stub&AbstractRouteDelegate $handlerSpy
         */
        $handlerSpy = $this->createStub(AbstractRouteDelegate::class);
        $sut = new RouteRegistry();
        $sut->addGet($givenRoute, $handlerSpy);
        $result = $sut->fromPost($givenRoute);
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestDox("Shall not register a \"POST\" request in the \"GET\" request registry.")]
    public function test5()
    {
        $givenRoute = "/";
        /**
         * @var Stub&AbstractRouteDelegate $handlerSpy
         */
        $handlerSpy = $this->createStub(AbstractRouteDelegate::class);
        $sut = new RouteRegistry();
        $sut->addPost($givenRoute, $handlerSpy);
        $result = $sut->fromGet($givenRoute);
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }
}
