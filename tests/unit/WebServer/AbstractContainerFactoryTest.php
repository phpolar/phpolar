<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(AbstractContainerFactory::class)]
final class AbstractContainerFactoryTest extends TestCase
{
    #[TestCase("Shall configure the PSR-11 container with the given configuration")]
    public function test1()
    {
        $configuration = new ContainerConfigurationStub();
        $configuration["test_case"] = "what?";
        $factory = static fn (ArrayAccess $config) => new ConfigurableContainerStub($config);
        $sut = new class ($factory) extends AbstractContainerFactory {
        };
        $container = $sut->getContainer($configuration);
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertSame($configuration["test_case"], $container->get("test_case"));
    }
}
