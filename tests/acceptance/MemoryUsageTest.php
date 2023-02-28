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
use Phpolar\Phpolar\Routing\AbstractContentDelegate;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingHandler;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\Storage\AbstractStorage;
use Phpolar\Phpolar\Storage\Item;
use Phpolar\Phpolar\Storage\ItemKey;
use Phpolar\Phpolar\Tests\Fakes\FakeModel;
use Phpolar\Phpolar\Tests\Fakes\ModelList;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\UriStub;
use Phpolar\Phpolar\WebServer\ContainerFactory;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\HtmlSafeContext;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;
use const Phpolar\Phpolar\Tests\TEST_GET_ROUTE;
use const Phpolar\Phpolar\Tests\TEST_POST_ROUTE;
use const Phpolar\Phpolar\Tests\FORM_TPL_PATH;
use const Phpolar\Phpolar\Tests\LIST_TPL_PATH;

final class MemoryUsageTest extends TestCase
{
    protected function getContainerFactory(RouteRegistry $routes): ContainerFactory
    {
        $config = new ContainerConfigurationStub();
        $config[RouteRegistry::class] = $routes;
        $config[RoutingMiddleware::class] = static fn (ArrayAccess $config) => new RoutingMiddleware($config[RoutingHandler::class]);
        $config[RoutingHandler::class] = static fn (ArrayAccess $config) => new RoutingHandler($config[RouteRegistry::class], $config[ResponseFactoryInterface::class], $config[StreamFactoryInterface::class], $config[WebServer::ERROR_HANDLER_401], $config[ContainerInterface::class]);
        $config[PrimaryHandler::class] = static fn (ArrayAccess $config) => new PrimaryHandler($config[WebServer::ERROR_HANDLER_404]);
        $config[WebServer::ERROR_HANDLER_404] = static fn (ArrayAccess $config) => new ErrorHandler(ResponseCode::NOT_FOUND, "Not Found", $config[ContainerInterface::class]);
        $config[WebServer::ERROR_HANDLER_401] = static fn (ArrayAccess $conf) => new ErrorHandler(401, "Unauthorized", $conf[ContainerInterface::class]);
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[TemplateEngine::class] = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[CsrfRequestCheckMiddleware::class] = static fn (ArrayAccess $config) => new CsrfRequestCheckMiddleware($config[RequestHandlerInterface::class]);
        $config[CsrfResponseFilterMiddleware::class] = static fn (ArrayAccess $config) => new CsrfResponseFilterMiddleware($config[AbstractTokenStorage::class], $config[CsrfTokenGenerator::class], $config[ResponseFilterStrategyInterface::class]);
        $config[CsrfTokenGenerator::class] = new CsrfTokenGenerator();
        $config[AbstractTokenStorage::class] = new SessionTokenStorage([REQUEST_ID_KEY => ""]);
        $config[ResponseFilterStrategyInterface::class] = $this->createStub(ResponseFilterStrategyInterface::class);
        $container = new ConfigurableContainerStub($config);
        return new ContainerFactory(static fn () => $container);
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
        $server = WebServer::createApp($containerFac, $config);
        $server->useRoutes($routes);
        $server->receive($request);
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) PROJECT_MEMORY_USAGE_THRESHOLD, $totalUsed);
    }
}
