<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Phpolar\CsrfProtection\CsrfTokenGenerator;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Http\ResponseFilterStrategyInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionTokenStorage;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\Phpolar\DependencyInjection\ClosureContainerFactory;
use Phpolar\Phpolar\DependencyInjection\ContainerFactoryInterface;
use Phpolar\Phpolar\DependencyInjection\ContainerManager;
use Phpolar\Phpolar\Routing\AbstractContentDelegate;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingHandler;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\PrimaryHandler;
use Phpolar\Phpolar\App;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;

final class MemoryUsageTest extends TestCase
{
    protected function getContainerFactory(RouteRegistry $routes): ContainerFactoryInterface
    {
        $config = new ContainerConfigurationStub();
        $config[RouteRegistry::class] = $routes;
        $config[RoutingMiddleware::class] = static fn (ArrayAccess $config) => new RoutingMiddleware($config[RoutingHandler::class]);
        $config[RoutingHandler::class] = static fn (ArrayAccess $config) => new RoutingHandler($config[RouteRegistry::class], $config[ResponseFactoryInterface::class], $config[StreamFactoryInterface::class], $config[App::ERROR_HANDLER_401], $config[ContainerInterface::class]);
        $config[PrimaryHandler::class] = static fn (ArrayAccess $config) => new PrimaryHandler($config[App::ERROR_HANDLER_404]);
        $config[App::ERROR_HANDLER_404] = static fn (ArrayAccess $config) => new ErrorHandler(ResponseCode::NOT_FOUND, "Not Found", $config[ContainerInterface::class]);
        $config[App::ERROR_HANDLER_401] = static fn (ArrayAccess $conf) => new ErrorHandler(401, "Unauthorized", $conf[ContainerInterface::class]);
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[TemplateEngine::class] = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[CsrfRequestCheckMiddleware::class] = static fn (ArrayAccess $config) => new CsrfRequestCheckMiddleware($config[RequestHandlerInterface::class]);
        $config[CsrfResponseFilterMiddleware::class] = static fn (ArrayAccess $config) => new CsrfResponseFilterMiddleware($config[AbstractTokenStorage::class], $config[CsrfTokenGenerator::class], $config[ResponseFilterStrategyInterface::class]);
        $config[CsrfTokenGenerator::class] = new CsrfTokenGenerator();
        $config[AbstractTokenStorage::class] = new SessionTokenStorage([REQUEST_ID_KEY => ""]);
        $config[ResponseFilterStrategyInterface::class] = $this->createStub(ResponseFilterStrategyInterface::class);
        $container = new ConfigurableContainerStub($config);
        return new ClosureContainerFactory(static fn () => $container);
    }

    #[TestDox("Memory usage shall be below \$threshold bytes")]
    public function test1(int|string $threshold = PROJECT_MEMORY_USAGE_THRESHOLD)
    {
        $this->expectOutputString("content");
        $request = new RequestStub("GET", "/");
        $config = new ContainerConfigurationStub();
        $routes = new RouteRegistry();
        /**
         * @var Stub&AbstractContentDelegate $contentDelStub
         */
        $contentDelStub = $this->createStub(AbstractContentDelegate::class);
        $contentDelStub->method("getResponseContent")->willReturn("content");
        $routes->add("GET", "/", $contentDelStub);
        $config[RouteRegistry::class] = $routes;
        $containerFac = $this->getContainerFactory($routes);
        $totalUsed = -memory_get_usage();
        $server = App::create(new ContainerManager($containerFac, $config));
        $server->useRoutes($routes);
        $server->receive($request);
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) $threshold, $totalUsed);
    }
}
