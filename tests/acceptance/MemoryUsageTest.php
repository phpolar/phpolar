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
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Phpolar\Phpolar\Http\RouteMap;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\App;
use Phpolar\Phpolar\DependencyInjection\ContainerLoader;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Phpolar\Http\RequestMethods;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;

#[TestDox("Low Memory Usage")]
final class MemoryUsageTest extends TestCase
{
    protected function getContainerFactory(RouteMap $routes): ContainerInterface
    {
        $config = new ContainerConfigurationStub();
        $config[RouteMap::class] = $routes;
        $config[RoutingMiddleware::class] = static fn (ArrayAccess $config) => new RoutingMiddleware($config[RoutingHandler::class]);
        $config[ModelResolverInterface::class] = $this->createStub(ModelResolverInterface::class);
        $config[RoutableResolverInterface::class] = new class () implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface|false
            {
                return $target;
            }
        };
        $config[RoutingHandler::class] = static fn (ArrayAccess $config) => new RoutingHandler(
            $config[RouteMap::class],
            $config[ResponseFactoryInterface::class],
            $config[StreamFactoryInterface::class],
            $config[ModelResolverInterface::class],
            new AuthorizationChecker(
                routableResolver: $config[RoutableResolverInterface::class],
                unauthHandler: new class () implements RequestHandlerInterface {
                    public function handle(ServerRequestInterface $request): ResponseInterface
                    {
                        return new ResponseStub(ResponseCode::UNAUTHORIZED, "Unauthorized");
                    }
                },
            ),
        );
        $config[MiddlewareQueueRequestHandler::class] = new MiddlewareQueueRequestHandler(
            new class () implements RequestHandlerInterface {
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return new ResponseStub(ResponseCode::NOT_FOUND, "Not Found");
                }
            }
        );
        $config[DiTokens::RESPONSE_EMITTER] = new SapiEmitter();
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[TemplateEngine::class] = new TemplateEngine();
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
        (new ContainerLoader())->load($container, $containerConfig);
        return $container;
    }

    #[TestDox("Memory usage shall be below \$threshold bytes")]
    public function test1(int|string $threshold = PROJECT_MEMORY_USAGE_THRESHOLD)
    {
        $this->expectOutputString("content");
        $request = new RequestStub("GET", "/");
        $config = new ContainerConfigurationStub();
        $propertyInjector = $this->createStub(PropertyInjectorInterface::class);
        $routes = new RouteMap($propertyInjector);
        /**
         * @var Stub&RoutableInterface $contentDelStub
         */
        $contentDelStub = $this->createStub(RoutableInterface::class);
        $contentDelStub->method("process")->willReturn("content");
        $routes->add(RequestMethods::GET, "/", $contentDelStub);
        $config[RouteMap::class] = $routes;
        $containerFac = $this->getContainerFactory($routes);
        $totalUsed = -memory_get_usage();
        $server = App::create($this->configureContainer($containerFac, $config));
        $server->receive($request);
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) $threshold, $totalUsed);
    }
}
