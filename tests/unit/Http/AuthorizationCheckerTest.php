<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use Phpolar\Phpolar\Auth\AbstractProtectedRoutable;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(AuthorizationChecker::class)]
final class AuthorizationCheckerTest extends TestCase
{
    #[TestDox("Shall return the decorated routable when authorization is successful")]
    public function testa()
    {
        $givenRoutable = $this->createStub(RequestProcessorInterface::class);
        $decoratedRoutable = $this->createStub(AbstractProtectedRoutable::class)->withUser((object)["id" => 123]);
        $routableResolverMock = $this->createMock(RequestProcessorResolverInterface::class);
        $routableResolverMock->method("resolve")->willReturn($decoratedRoutable);
        $unauthHander = $this->createMock(RequestHandlerInterface::class);
        $unauthHander->method("handle")->willReturn(new ResponseStub(ResponseCode::Unauthorized->value));
        $sut = new AuthorizationChecker($routableResolverMock, $unauthHander);
        $result = $sut->authorize($givenRoutable, new RequestStub());
        $this->assertSame($decoratedRoutable, $result);
    }

    #[TestDox("Shall return an Unauthorized HTTP response when authorization is not successful")]
    public function testb()
    {
        $givenRoutable = $this->createStub(RequestProcessorInterface::class);
        $routableResolverMock = $this->createMock(RequestProcessorResolverInterface::class);
        $routableResolverMock->method("resolve")->willReturn(false);
        $unauthHander = $this->createMock(RequestHandlerInterface::class);
        $unauthHander->method("handle")->willReturn(new ResponseStub(ResponseCode::Unauthorized->value));
        $sut = new AuthorizationChecker($routableResolverMock, $unauthHander);
        $result = $sut->authorize($givenRoutable, new RequestStub());
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(ResponseCode::Unauthorized->value, $result->getStatusCode());
    }
}
