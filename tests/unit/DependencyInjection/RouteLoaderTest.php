<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteLoader::class)]
#[RunTestsInSeparateProcesses]
final class RouteLoaderTest extends TestCase
{
    #[TestDox("Shall load routes into the container")]
    public function test4()
    {
        $containerConfig = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($containerConfig);
        $sut = new RouteLoader($containerConfig);
        $sut->loadRoutes(new RouteRegistry());
        $this->assertNotEmpty($container->get(RouteRegistry::class));
    }
}
