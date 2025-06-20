<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(AuthorizationChecker::class)]
final class AuthorizationCheckerTest extends TestCase
{
    #[TestDox("Shall return a routable when authorization is successful")]
    public function testa()
    {
        $givenRoutable = $this->createStub(RoutableInterface::class);
        $routableResolverMock = $this->createMock(RoutableResolverInterface::class);
        $routableResolverMock->method("resolve")->willReturn($givenRoutable);
        $unauthHander = $this->createMock(RequestHandlerInterface::class);
        $unauthHander->method("handle")->willReturn(new ResponseStub(ResponseCode::UNAUTHORIZED));
        $sut = new AuthorizationChecker($routableResolverMock, $unauthHander);
        $result = $sut->authorize($givenRoutable, new RequestStub());
        $this->assertInstanceOf(RoutableInterface::class, $result);
    }

    #[TestDox("Shall return an Unauthorized HTTP response when authorization is not successful")]
    public function testb()
    {
        $givenRoutable = $this->createStub(RoutableInterface::class);
        $routableResolverMock = $this->createMock(RoutableResolverInterface::class);
        $routableResolverMock->method("resolve")->willReturn(false);
        $unauthHander = $this->createMock(RequestHandlerInterface::class);
        $unauthHander->method("handle")->willReturn(new ResponseStub(ResponseCode::UNAUTHORIZED));
        $sut = new AuthorizationChecker($routableResolverMock, $unauthHander);
        $result = $sut->authorize($givenRoutable, new RequestStub());
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(ResponseCode::UNAUTHORIZED, $result->getStatusCode());
    }
}
