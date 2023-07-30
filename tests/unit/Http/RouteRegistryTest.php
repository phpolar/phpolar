<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use DomainException;
use Generator;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\Phpolar\Core\Routing\RouteNotRegistered;
use Phpolar\Phpolar\Core\Routing\RouteParamMap;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteRegistry::class)]
#[CoversClass(ResolvedRoute::class)]
#[UsesClass(RouteParamMap::class)]
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

    public static function notImplementedMethods(): Generator
    {
        yield ["PUT"];
        yield ["DELETE"];
        yield ["PATCH"];
    }

    #[TestDox("Shall retrieve the request handlers associated with a \$requestMethod request")]
    #[DataProvider("requestMethods")]
    public function test1(string $requestMethod)
    {
        $givenRoute = "/";
        /**
         * @var MockObject&RoutableInterface $handlerSpy
         */
        $handlerSpy = $this->createMock(RoutableInterface::class);
        $handlerSpy->expects($this->once())->method("process")->willReturn("");
        $sut = new RouteRegistry();
        $sut->add($requestMethod, $givenRoute, $handlerSpy);
        $registeredHandler = $sut->match(new RequestStub($requestMethod, $givenRoute), $givenRoute);
        $registeredHandler->process(new ConfigurableContainerStub(new ContainerConfigurationStub()));
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
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();
        $sut->add($methodA, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($methodB, $givenRoute), $givenRoute);
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith(["GET", "/some/path/{id}", "/some/path/123"])]
    #[TestWith(["GET", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith(["GET", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["GET", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["GET", "", ""])]
    #[TestWith(["POST", "/some/path/{id}", "/some/path/123"])]
    #[TestWith(["POST", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith(["POST", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["POST", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall match a route with params to the correct handler with parsed route params. \$givenRoute matched \$givenRequestPath")]
    public function testa(string $method, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($method, $givenRequestPath));
        $this->assertNotInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith(["GET", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith(["GET", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith(["GET", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["GET", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith(["GET", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["GET", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["GET", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith(["POST", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith(["POST", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith(["POST", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith(["POST", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall not match a route with params when the path is not a complete match. \$givenRoute did not match \$givenRequestPath")]
    public function testb(string $method, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($method, $givenRequestPath));
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
        $sut = new RouteRegistry();
        $result = $sut->match(new RequestStub($method, $givenRequestPath));
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[TestWith(["GET", "/{invalid^}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith(["POST", "/some/path/{invalid%}", "/some/path/123"])]
    #[TestDox("Shall not match a route when the route param is invalid")]
    public function testd(string $method, string $givenRoute, string $givenRequestPath)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();
        $sut->add($method, $givenRoute, $handlerStub);
        $result = $sut->match(new RequestStub($method, $givenRequestPath));
        $this->assertInstanceOf(RouteNotRegistered::class, $result);
    }

    #[DataProvider("notImplementedMethods")]
    #[TestDox("Shall throw an exception when attempting to add a non-supported method. Attempted \$method")]
    public function teste(string $method, string $givenRoute = "/")
    {
        $this->expectException(DomainException::class);
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();
        $sut->add($method, $givenRoute, $handlerStub);
    }

    #[DataProvider("requestMethods")]
    #[TestDox("Shall match a route with params when multiple routes are registered")]
    public function testf(string $requestMethod)
    {
        /**
         * @var Stub&RoutableInterface $handlerStub
         */
        $handlerStub = $this->createStub(RoutableInterface::class);
        $sut = new RouteRegistry();

        $routes = [
            "/",
            "/no-params",
            "/something/{id}/something-else/{anotherId}",
        ];

        $requestPath = "/something/123/something-else/456";

        foreach ($routes as $route) {
            $sut->add($requestMethod, $route, $handlerStub);
        }

        $result = $sut->match(new RequestStub($requestMethod, $requestPath));
        $this->assertNotInstanceOf(RouteNotRegistered::class, $result);
    }
}
