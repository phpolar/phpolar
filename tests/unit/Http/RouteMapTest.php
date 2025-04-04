<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Generator;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\RoutableFactory\RoutableFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteMap::class)]
#[CoversClass(ResolvedRoute::class)]
final class RouteMapTest extends TestCase
{
    private function getPropertyInjectorStub(): PropertyInjectorInterface
    {
        return $this->createStub(PropertyInjectorInterface::class);
    }

    public static function requestMethods(): Generator
    {
        yield [RequestMethods::GET, "GET"];
        yield [RequestMethods::POST, "POST"];
    }

    public static function nonMatchingMethods(): Generator
    {
        yield [RequestMethods::GET, RequestMethods::POST, "GET", "POST"];
        yield [RequestMethods::POST, RequestMethods::GET, "POST", "GET"];
    }

    #[TestDox("Shall retrieve the request handlers associated with a \$requestMethodString request")]
    #[DataProvider("requestMethods")]
    public function test1(RequestMethods $requestMethod, string $requestMethodString)
    {
        $givenRoute = "/";
        /**
         * @var MockObject&RoutableInterface $handlerSpy
         */
        $handlerSpy = $this->createMock(RoutableInterface::class);
        $handlerSpy->expects($this->once())->method("process")->willReturn("");
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($requestMethod, $givenRoute, $handlerSpy);
        $registeredHandler = $sut->match(new RequestStub($requestMethodString, $givenRoute), $givenRoute);
        $registeredHandler->process(new ConfigurableContainerStub(new ContainerConfigurationStub()));
    }

    #[TestDox("Shall return a RouteNotRegistered instance when a path of a \$requestMethodString request is not associated with any handlers.")]
    #[DataProvider("requestMethods")]
    public function test2(RequestMethods $requestMethod, string $requestMethodString)
    {
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $result = $sut->match(new RequestStub($requestMethodString), "an_unregistered_path");
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestDox("Shall not associate a \$methodAString request with a \$methodBString request.")]
    #[DataProvider("nonMatchingMethods")]
    public function test3(RequestMethods $methodA, RequestMethods $methodB, string $methodAString, string $methodBString)
    {
        $givenRoute = "/";
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($methodA, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($methodBString, $givenRoute), $givenRoute);
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith([RequestMethods::GET, "GET", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([RequestMethods::GET, "GET", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith([RequestMethods::GET, "GET", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([RequestMethods::GET, "GET", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::GET, "GET", "", ""])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([RequestMethods::POST, "POST", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall match a route with params to the correct handler with parsed route params. \$givenRoute matched \$givenRequestPath")]
    public function testa(RequestMethods $method, string $methodString, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($methodString, $givenRequestPath));
        $this->assertNotInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith([RequestMethods::GET, "GET", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith([RequestMethods::GET, "GET", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith([RequestMethods::GET, "GET", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([RequestMethods::GET, "GET", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith([RequestMethods::GET, "GET", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::GET, "GET", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::GET, "GET", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith([RequestMethods::POST, "POST", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::POST, "POST", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::POST, "POST", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall not match a route with params when the path is not a complete match. \$givenRoute did not match \$givenRequestPath")]
    public function testb(RequestMethods $method, string $methodString, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($methodString, $givenRequestPath));
        $this->assertInstanceOf(RouteNotRegistered::class, $result, $givenRequestPath);
    }

    #[TestWith(["GET", "/some/path/123"])]
    #[TestWith(["GET", "/some/path/abcdefg"])]
    #[TestWith(["GET", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["GET", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith(["GET", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["GET", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "/some/path/123"])]
    #[TestWith(["POST", "/some/path/abcdefg"])]
    #[TestWith(["POST", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["POST", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith(["POST", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall not match a route with params when the route was not registered. \$givenRequestPath did not match")]
    public function testc(string $method, string $givenRequestPath)
    {
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $result = $sut->match(new RequestStub($method, $givenRequestPath));
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith([RequestMethods::GET, "GET", "/{invalid^}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{invalid%}", "/some/path/123"])]
    #[TestDox("Shall not match a route when the route param is invalid")]
    public function testd(RequestMethods $method, string $methodString, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($methodString, $givenRequestPath));
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[DataProvider("requestMethods")]
    #[TestDox("Shall match a route with params when multiple routes are registered")]
    public function testf(RequestMethods $requestMethod, string $requestMethodString)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteMap($this->getPropertyInjectorStub());

        $routes = [
            "/",
            "/no-params",
            "/something/{id}/something-else/{anotherId}",
        ];

        $requestPath = "/something/123/something-else/456";

        foreach ($routes as $route) {
            $sut->add($requestMethod, $route, $handlerStub);
        }

        $result = $sut->match(new RequestStub($requestMethodString, $requestPath));
        $this->assertNotInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith([RequestMethods::GET, "GET", "/some/path"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path"])]
    #[TestDox("Shall call inject on property injector when a route target is matched")]
    public function testg(RequestMethods $method, string $methodString, string $givenRoute)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        /**
         * @var MockObject&PropertyInjectorInterface
         */
        $propertyInjectorMock = $this->createMock(PropertyInjectorInterface::class);
        $propertyInjectorMock->expects($this->once())->method("inject")->with($handlerStub);
        $sut = new RouteMap($propertyInjectorMock);
        $sut->add($method, $givenRoute, $handlerStub);
        $sut->match(new RequestStub($methodString, $givenRoute));
    }

    #[TestWith([RequestMethods::GET, "GET", "/some/path"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path"])]
    #[TestDox("Shall call createInstance on routable factory when matching a route without params")]
    public function testh(RequestMethods $method, string $methodString, string $givenRoute)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        /**
         * @var MockObject&RoutableFactoryInterface
         */
        $routeFactoryMock = $this->createMock(RoutableFactoryInterface::class);
        $routeFactoryMock->expects($this->once())->method("createInstance")->willReturn($handlerStub);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($method, $givenRoute, $routeFactoryMock);
        $sut->match(new RequestStub($methodString, $givenRoute));
    }

    #[TestWith([RequestMethods::GET, "GET", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([RequestMethods::POST, "POST", "/some/path/{id}", "/some/path/123"])]
    #[TestDox("Shall call createInstance on routable factory when matching a route with params")]
    public function testj(RequestMethods $method, string $methodString, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        /**
         * @var MockObject&RoutableFactoryInterface
         */
        $routeFactoryMock = $this->createMock(RoutableFactoryInterface::class);
        $routeFactoryMock->expects($this->once())->method("createInstance")->willReturn($handlerStub);
        $sut = new RouteMap($this->getPropertyInjectorStub());
        $sut->add($method, $givenRoute, $routeFactoryMock);
        $sut->match(new RequestStub($methodString, $givenRequestPath));
    }
}
