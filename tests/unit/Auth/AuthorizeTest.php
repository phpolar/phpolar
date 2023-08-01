<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Generator;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Authenticator\AuthenticatorInterface;
use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionMethod;

#[CoversClass(Authorize::class)]
#[CoversClass(User::class)]
final class AuthorizeTest extends TestCase
{
    public static function getContainerStub(): Generator
    {
        yield [new ConfigurableContainerStub(new ContainerConfigurationStub())];
    }

    #[TestDox("Shall return the target delegate when the request is authenticated")]
    #[DataProvider("getContainerStub")]
    public function testa(ContainerInterface $container)
    {
        $expectedContent = "<h1>I AM THE TARGET HANDLER</h1>";
        /**
         * @var AuthenticatorInterface&MockObject
         */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(true);
        $authenticatorMock->method("getUser")->willReturn(["name" => "", "avatarUrl" => "", "nickname" => "", "email" => ""]);
        $hostClass = new class ($expectedContent) extends AbstractProtectedRoutable
        {
            public function __construct(public string $content)
            {
            }

            #[Authorize]
            public function process(ContainerInterface $container): string
            {
                return $this->content;
            }
        };
        $reflectionMethod = new ReflectionMethod($hostClass, "process");
        $authenticateAttrs = $reflectionMethod->getAttributes(Authorize::class);
        /**
         * @var Authorize
         */
        $authenticateAttr = $authenticateAttrs[0]->newInstance();
        $result = $authenticateAttr->getResolvedRoutable(target: $hostClass, authenticator: $authenticatorMock);
        $this->assertEquals($hostClass->process($container), $result->process($container));
    }

    #[TestDox("Shall return false when the request is not authenticated")]
    #[DataProvider("getContainerStub")]
    public function testb(ContainerInterface $container)
    {
        $expectedContent = "<h1>I AM THE FALLBACK HANDLER</h1>";
        /**
         * @var RoutableInterface&Stub
         */
        $targetDelegateStub = $this->createStub(AbstractProtectedRoutable::class);
        $targetDelegateStub->method("process")->willReturn("<h1>I AM THE TARGET HANDLER</h1>");
        /**
         * @var AuthenticatorInterface&MockObject
         */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(false);
        $hostClass = new class ($expectedContent) implements RoutableInterface
        {
            public function __construct(public string $content)
            {
            }

            #[Authorize]
            public function process(ContainerInterface $container): string
            {
                return $this->content;
            }
        };
        $reflectionMethod = new ReflectionMethod($hostClass, "process");
        $authenticateAttrs = $reflectionMethod->getAttributes(Authorize::class);
        /**
         * @var Authorize
         */
        $authenticateAttr = $authenticateAttrs[0]->newInstance();
        $result = $authenticateAttr->getResolvedRoutable(target: $targetDelegateStub, authenticator: $authenticatorMock);
        $this->assertFalse($result);
    }

    #[TestDox("Shall add user credentials to the target delegate when the request is authenticated")]
    public function testc()
    {
        $authenticatedUser = ["name" => "FAKE NAME", "nickname" => "FAKE NICKNAME", "email" => "FAKE EMAIL", "avatarUrl" => "FAKE AVATAR URL"];
        $expectedContent = "<h1>I AM THE TARGET HANDLER</h1>";
        /**
         * @var AuthenticatorInterface&MockObject
         */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        $authenticatorMock->method("isAuthenticated")->willReturn(true);
        $authenticatorMock->method("getUser")->willReturn($authenticatedUser);
        $hostClass = new class ($expectedContent) extends AbstractProtectedRoutable
        {
            public function __construct(public string $content)
            {
            }

            #[Authorize]
            public function process(ContainerInterface $container): string
            {
                return $this->content;
            }
        };
        $reflectionMethod = new ReflectionMethod($hostClass, "process");
        $authenticateAttrs = $reflectionMethod->getAttributes(Authorize::class);
        /**
         * @var Authorize
         */
        $authenticateAttr = $authenticateAttrs[0]->newInstance();
        $result = $authenticateAttr->getResolvedRoutable(target: $hostClass, authenticator: $authenticatorMock);
        $this->assertEquals($result->user, new User(...$authenticatedUser));
    }
}
