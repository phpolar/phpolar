<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(ContainerLoader::class)]
#[RunTestsInSeparateProcesses]
final class ContainerLoaderTest extends TestCase
{
    #[TestDox("Shall load configuration from framework dependency files into container")]
    public function test1()
    {
        $containerConfig = new ContainerConfigurationStub();
        $containerConfig[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $containerConfig[StreamFactoryInterface::class] = new StreamFactoryStub("r");
        $container = new ConfigurableContainerStub($containerConfig);
        (new ContainerLoader())->load($container, $containerConfig);
        $this->assertNotEmpty($container->get(ResponseFactoryInterface::class));
        $this->assertNotEmpty($container->get(StreamFactoryInterface::class));
    }

    #[TestDox("Shall load custom configuration from files into container")]
    public function test2()
    {
        $dir = getcwd();
        chdir("tests/__fakes__");
        $containerConfig = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($containerConfig);
        (new ContainerLoader())->load($container, $containerConfig);
        chdir($dir);
        $this->assertNotEmpty($container->get(ResponseFactoryInterface::class));
        $this->assertNotEmpty($container->get(StreamFactoryInterface::class));
    }

    #[TestDox("Shall not load the container if the files do not exist")]
    public function test3()
    {
        $dir = getcwd();
        chdir(__DIR__);
        $containerConfig = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($containerConfig);
        (new ContainerLoader())->load($container, $containerConfig);
        chdir($dir);
        $this->assertCount(0, $containerConfig);
    }
}
