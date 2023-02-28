<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(ContainerLoader::class)]
final class ContainerLoaderTest extends TestCase
{
    #[TestDox("Shall load configuration from framework dependency files into container")]
    #[Group("me")]
    public function test1()
    {
        $containerConfig = new ContainerConfigurationStub();
        $containerConfig[RouteRegistry::class] = $this->createStub(RouteRegistry::class);
        $containerConfig[TemplateEngine::class] = static fn (ArrayAccess $config) => new TemplateEngine($config[TemplatingStrategyInterface::class], new Binder(), new Dispatcher());
        $containerConfig[TemplatingStrategyInterface::class] = $this->createStub(TemplatingStrategyInterface::class);
        $containerConfig[ContainerInterface::class] = new ConfigurableContainerStub($containerConfig);
        $containerConfig[ResponseFactoryInterface::class] = $this->createStub(ResponseFactoryInterface::class);
        $containerConfig[StreamFactoryInterface::class] = $this->createStub(StreamFactoryInterface::class);
        new ContainerLoader($containerConfig);
        $this->assertNotEmpty($containerConfig[RoutingMiddleware::class]);
        $this->assertNotEmpty($containerConfig[WebServer::ERROR_HANDLER_401]);
    }

    #[TestDox("Shall load custom configuration from files into container")]
    public function test2()
    {
        $dir = getcwd();
        chdir("tests/__fakes__");
        $containerConfig = new ContainerConfigurationStub();
        new ContainerLoader($containerConfig);
        chdir($dir);
        $this->assertNotEmpty($containerConfig[PrimaryHandler::class]);
    }

    #[TestDox("Shall not load the container if the files do not exist")]
    public function test3()
    {
        $dir = getcwd();
        chdir(__DIR__);
        $containerConfig = new ContainerConfigurationStub();
        new ContainerLoader($containerConfig);
        chdir($dir);
        $this->assertCount(0, $containerConfig);
    }

    #[TestDox("Shall load routes into the container")]
    public function test4()
    {
        $containerConfig = new ContainerConfigurationStub();
        $sut = new ContainerLoader($containerConfig);
        $sut->loadRoutes(new RouteRegistry());
        $this->assertNotEmpty($containerConfig[RouteRegistry::class]);
    }
}