<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(AbstractContainerFactory::class)]
#[CoversClass(ContainerFactory::class)]
final class AbstractContainerFactoryTest extends TestCase
{
    #[TestDox("Shall configure the PSR-11 container with the given configuration")]
    public function test1()
    {
        $configuration = new ContainerConfigurationStub();
        $configuration["test_case"] = "what?";
        $factory = static fn (ArrayAccess $config) => new ConfigurableContainerStub($config);
        $sut = new ContainerFactory($factory);
        $container = $sut->getContainer($configuration);
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertSame($configuration["test_case"], $container->get("test_case"));
    }
}
