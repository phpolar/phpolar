<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Routing;

use Generator;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteRegistry::class)]
final class RouteRegistryTest extends TestCase
{
    public static function requestMethods(): Generator
    {
        yield ["GET"];
        yield ["POST"];
    }

    public static function nonMatchingMethods(): Generator
    {
        yield ["GET", "POST"];
        yield ["POST", "GET"];
    }

    #[TestDox("Shall retrieve the request handlers associated with a \$requestMethod request")]
    #[DataProvider("requestMethods")]
    public function test1(string $requestMethod)
    {
        $givenRoute = "/";
        /**
         * @var MockObject&AbstractContentDelegate $handlerSpy
         */
        $handlerSpy = $this->getMockForAbstractClass(AbstractContentDelegate::class);
        $handlerSpy->expects($this->once())->method("getResponseContent")->willReturn("");
        $sut = new RouteRegistry();
        $sut->add($requestMethod, $givenRoute, $handlerSpy);
        $registeredHandler = $sut->match(new RequestStub($requestMethod, $givenRoute), $givenRoute);
        $registeredHandler->getResponseContent(new ConfigurableContainerStub(new ContainerConfigurationStub()));
    }

    #[TestDox("Shall return a RouteNotRegistered instance when a path of a \$requestMethod request is not associated with any handlers.")]
    #[DataProvider("requestMethods")]
    public function test2(string $requestMethod)
    {
        $sut = new RouteRegistry();
        $result = $sut->match(new RequestStub($requestMethod), "an_unregistered_path");
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestDox("Shall not associate a \$methodA request with a \$methodB request.")]
    #[DataProvider("nonMatchingMethods")]
    public function test3(string $methodA, string $methodB)
    {
        $givenRoute = "/";
        /**
         * @var Stub&AbstractContentDelegate $handlerSpy
         */
        $handlerSpy = $this->createStub(AbstractContentDelegate::class);
        $sut = new RouteRegistry();
        $sut->add($methodA, $givenRoute, $handlerSpy);
        $result = $sut->match(new RequestStub($methodB, $givenRoute), $givenRoute);
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }
}
