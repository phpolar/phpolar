<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use PhpContrib\Http\Message\ResponseFilterInterface;
use Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionWrapper;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\Model\ParsedBodyResolver;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\App;
use Phpolar\Phpolar\DependencyInjection\ContainerLoader;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Phpolar\Http\Representations;
use Phpolar\Phpolar\Http\RequestProcessingHandler;
use Phpolar\Phpolar\Http\RequestProcessorExecutor;
use Phpolar\Phpolar\Http\ResponseBuilder;
use Phpolar\Phpolar\Http\ResponseBuilderInterface;
use Phpolar\Phpolar\Http\Server;
use Phpolar\Phpolar\Http\ServerInterface;
use Phpolar\Phpolar\Http\Target;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;

#[TestDox("Low Memory Usage")]
final class MemoryUsageTest extends TestCase
{
    protected function getContainerFactory(ServerInterface $server): ContainerInterface
    {
        $modelResolver = new ParsedBodyResolver($_REQUEST);
        $config = new ContainerConfigurationStub();
        $config[ServerInterface::class] = $server;
        $config[RoutingMiddleware::class] = static fn(ArrayAccess $config) => new RoutingMiddleware($config[RequestProcessingHandler::class]);
        $config[ModelResolverInterface::class] = $modelResolver;
        $config[RoutableResolverInterface::class] = new class() implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface|false
            {
                return $target;
            }
        };
        $config[RequestProcessingHandler::class] = static fn(ArrayAccess $config) => new RequestProcessingHandler(
            propertyInjector: $config[PropertyInjectorInterface::class],
            processorExecutor: $config[RequestProcessorExecutor::class],
            server: $config[ServerInterface::class],
            responseBuilder: $config[ResponseBuilderInterface::class],
            authChecker: new AuthorizationChecker(
                routableResolver: $config[RoutableResolverInterface::class],
                unauthHandler: new class() implements RequestHandlerInterface {
                    public function handle(ServerRequestInterface $request): ResponseInterface
                    {
                        return new ResponseStub(ResponseCode::UNAUTHORIZED, "Unauthorized");
                    }
                },
            ),
            modelResolver: $modelResolver,
        );
        $config[MiddlewareQueueRequestHandler::class] = new MiddlewareQueueRequestHandler(
            new class() implements RequestHandlerInterface {
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return new ResponseStub(ResponseCode::NOT_FOUND, "Not Found");
                }
            }
        );
        $config[DiTokens::RESPONSE_EMITTER] = new SapiEmitter();
        $config[ContainerInterface::class] = static fn(ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseBuilderInterface::class] = static fn(ArrayAccess $conf) => new ResponseBuilder(
            responseFactory: $conf[ResponseFactoryInterface::class],
            streamFactory: $conf[StreamFactoryInterface::class],
        );
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[TemplateEngine::class] = new TemplateEngine();
        $config[TemplatingStrategyInterface::class] = new StreamContentStrategy();
        $config[ContainerInterface::class] = new ConfigurableContainerStub($config);
        $config[DiTokens::CSRF_CHECK_MIDDLEWARE] = static fn(ArrayAccess $config) => new CsrfRequestCheckMiddleware($config[RequestHandlerInterface::class]);
        $config[DiTokens::CSRF_RESPONSE_FILTER_MIDDLEWARE] = static fn(ArrayAccess $config) => new CsrfResponseFilterMiddleware(responseFilter: $config[ResponseFilterInterface::class]);
        $session = [REQUEST_ID_KEY => ""];
        $config[AbstractTokenStorage::class] = new SessionTokenStorage(new SessionWrapper($session));
        $config[RequestProcessorExecutor::class] = new RequestProcessorExecutor();
        $config[PropertyInjectorInterface::class] = new class() implements PropertyInjectorInterface {
            public function inject(object $injectee): void
            {
                // intentionally empty
            }
        };
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
        $request->withHeader("Accept", MimeType::TextHtml->value);
        $config = new ContainerConfigurationStub();

        $server = new Server([
            new Target(
                location: "/",
                method: HttpMethod::Get,
                representations: new Representations([
                    MimeType::TextHtml,
                ]),
                requestProcessor: new class() implements RoutableInterface {
                    public function process(): string
                    {
                        return "content";
                    }
                }
            ),
        ]);

        $containerFac = $this->getContainerFactory($server);
        $totalUsed = -memory_get_usage();
        $app = App::create($this->configureContainer($containerFac, $config));
        $app->receive($request);
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) $threshold, $totalUsed);
    }
}
