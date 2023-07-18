<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Phpolar\CsrfProtection\CsrfTokenGenerator;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionWrapper;
use Phpolar\Http\Message\ResponseFilterStrategyInterface;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Http\RoutableInterface;
use Phpolar\Phpolar\Http\RouteRegistry;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\App;
use Phpolar\Phpolar\DependencyInjection\ContainerLoader;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
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

#[TestDox("Low Memory Usage")]
final class MemoryUsageTest extends TestCase
{
    protected function getContainerFactory(RouteRegistry $routes): ContainerInterface
    {
        $config = new ContainerConfigurationStub();
        $config[RouteRegistry::class] = $routes;
        $config[RoutingMiddleware::class] = static fn (ArrayAccess $config) => new RoutingMiddleware($config[RoutingHandler::class]);
        $config[ModelResolverInterface::class] = $this->createStub(ModelResolverInterface::class);
        $config[RoutableResolverInterface::class] = new class () implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface|false
            {
                return $target;
            }
        };
        $config[RoutingHandler::class] = static fn (ArrayAccess $config) => new RoutingHandler(
            $config[RouteRegistry::class],
            $config[ResponseFactoryInterface::class],
            $config[StreamFactoryInterface::class],
            $config[ContainerInterface::class],
            $config[ModelResolverInterface::class],
            routableResolver: $config[RoutableResolverInterface::class],
        );
        $config[MiddlewareQueueRequestHandler::class] = static fn (ArrayAccess $config) => new MiddlewareQueueRequestHandler($config[DiTokens::ERROR_HANDLER_404]);
        $config[DiTokens::ERROR_HANDLER_404] = static fn (ArrayAccess $config) => new ErrorHandler(ResponseCode::NOT_FOUND, "Not Found", $config[ContainerInterface::class]);
        $config[DiTokens::ERROR_HANDLER_401] = static fn (ArrayAccess $conf) => new ErrorHandler(401, "Unauthorized", $conf[ContainerInterface::class]);
        $config[DiTokens::RESPONSE_EMITTER] = new SapiEmitter();
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[TemplateEngine::class] = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[DiTokens::CSRF_CHECK_MIDDLEWARE] = static fn (ArrayAccess $config) => new CsrfRequestCheckMiddleware($config[RequestHandlerInterface::class]);
        $config[DiTokens::CSRF_RESPONSE_FILTER_MIDDLEWARE] = static fn (ArrayAccess $config) => new CsrfResponseFilterMiddleware($config[AbstractTokenStorage::class], $config[CsrfTokenGenerator::class], $config[ResponseFilterStrategyInterface::class]);
        $session = [REQUEST_ID_KEY => ""];
        $config[AbstractTokenStorage::class] = new SessionTokenStorage(new SessionWrapper($session));
        $config[ResponseFilterStrategyInterface::class] = $this->createStub(ResponseFilterStrategyInterface::class);
        return new ConfigurableContainerStub($config);
    }

    private function configureContainer(ContainerInterface $container, ArrayAccess $containerConfig): ContainerInterface
    {
        (new ContainerLoader())->load($containerConfig, $container);
        return $container;
    }

    #[TestDox("Memory usage shall be below \$threshold bytes")]
    public function test1(int|string $threshold = PROJECT_MEMORY_USAGE_THRESHOLD)
    {
        $this->expectOutputString("content");
        $request = new RequestStub("GET", "/");
        $config = new ContainerConfigurationStub();
        $routes = new RouteRegistry();
        /**
         * @var Stub&RoutableInterface $contentDelStub
         */
        $contentDelStub = $this->createStub(RoutableInterface::class);
        $contentDelStub->method("process")->willReturn("content");
        $routes->add("GET", "/", $contentDelStub);
        $config[RouteRegistry::class] = $routes;
        $containerFac = $this->getContainerFactory($routes);
        $totalUsed = -memory_get_usage();
        $server = App::create($this->configureContainer($containerFac, $config));
        $server->receive($request);
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) $threshold, $totalUsed);
    }
}
