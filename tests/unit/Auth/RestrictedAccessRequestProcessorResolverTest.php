<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\Phpolar\Auth\AbstractRestrictedAccessRequestProcessor;
use Phpolar\Phpolar\Auth\Authorize;
use Phpolar\Phpolar\Auth\RestrictedAccessRequestProcessorResolver;
use Phpolar\Phpolar\Auth\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RestrictedAccessRequestProcessorResolver::class)]
#[CoversClass(AbstractRestrictedAccessRequestProcessor::class)]
#[UsesClass(Authorize::class)]
#[UsesClass(User::class)]
final class RestrictedAccessRequestProcessorResolverTest extends TestCase
{
    #[TestDox("Shall return the false when the authenticator returns null")]
    public function testa()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorStub = $this->createStub(AuthenticatorInterface::class);
        $authenticatorStub->method("isAuthenticated")->willReturn(false);
        $sut = new RestrictedAccessRequestProcessorResolver($authenticatorStub);
        $target = new class () extends AbstractRestrictedAccessRequestProcessor
        {
            #[Authorize]
            public function process(): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertFalse($result);
    }

    #[TestDox("Shall return the target routable when the authenticator returns an object")]
    public function testb()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorMock = $this->createStub(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(true);
        $authenticatorMock->method("getUser")->willReturn(["name" => "", "nickname" => "", "email" => "", "avatarUrl" => ""]);
        $sut = new RestrictedAccessRequestProcessorResolver($authenticatorMock);
        $target = new class () extends AbstractRestrictedAccessRequestProcessor
        {
            #[Authorize]
            public function process(): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertInstanceOf($target::class, $result);
    }

    #[TestDox("Shall return the target routable when the target routable is not configured for authentication")]
    public function testc()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorMock = $this->createStub(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(true);
        $sut = new RestrictedAccessRequestProcessorResolver($authenticatorMock);
        $target = new class () extends AbstractRestrictedAccessRequestProcessor
        {
            public function process(): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertInstanceOf($target::class, $result);
    }

    #[TestDox("Shall return the target routable when it is NOT an instance of AbstractRestrictedAccessRequestProcessor")]
    public function testd()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorMock = $this->createStub(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(false);
        $sut = new RestrictedAccessRequestProcessorResolver($authenticatorMock);
        $target = new class () implements RequestProcessorInterface
        {
            public function process(): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertInstanceOf($target::class, $result);
    }
}
