<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Authenticator\AuthenticatorInterface;
use Phpolar\Phpolar\Auth\AbstractProtectedRoutable;
use Phpolar\Phpolar\Auth\Authorize;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(ProtectedRoutableResolver::class)]
final class ProtectedRoutableResolverTest extends TestCase
{
    #[TestDox("Shall return the false when the authenticator returns null")]
    public function testa()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorStub = $this->createStub(AuthenticatorInterface::class);
        $authenticatorStub->method("isAuthenticated")->willReturn(false);
        $sut = new ProtectedRoutableResolver($authenticatorStub);
        $target = new class () extends AbstractProtectedRoutable
        {
            #[Authorize]
            public function process(ContainerInterface $container): string
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
        $sut = new ProtectedRoutableResolver($authenticatorMock);
        $target = new class () extends AbstractProtectedRoutable
        {
            #[Authorize]
            public function process(ContainerInterface $container): string
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
        $sut = new ProtectedRoutableResolver($authenticatorMock);
        $target = new class () extends AbstractProtectedRoutable
        {
            public function process(ContainerInterface $container): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertInstanceOf($target::class, $result);
    }

    #[TestDox("Shall return the target routable when it is NOT an instance of AbstractProtectedRoutable")]
    public function testd()
    {
        /**
         * @var Stub&AuthenticatorInterface
         */
        $authenticatorMock = $this->createStub(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(false);
        $sut = new ProtectedRoutableResolver($authenticatorMock);
        $target = new class () implements RoutableInterface
        {
            public function process(ContainerInterface $container): string
            {
                return "";
            }
        };
        $result = $sut->resolve(target: $target);
        $this->assertInstanceOf($target::class, $result);
    }
}
