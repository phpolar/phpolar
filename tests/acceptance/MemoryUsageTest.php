<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use ArrayAccess;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Phpolar\Routing\AbstractRouteDelegate;
use Phpolar\Phpolar\Routing\DefaultRoutingHandler;
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Storage\AbstractStorage;
use Phpolar\Phpolar\Storage\Item;
use Phpolar\Phpolar\Storage\ItemKey;
use Phpolar\Phpolar\Storage\Key;
use Phpolar\Phpolar\Tests\Fakes\FakeModel;
use Phpolar\Phpolar\Tests\Fakes\ModelList;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\UriStub;
use Phpolar\Phpolar\WebServer\ContainerFactory;
use Phpolar\Phpolar\WebServer\MiddlewareProcessingQueue;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\HtmlSafeContext;
use Phpolar\PhpTemplating\StreamContentStrategy;
use Phpolar\PhpTemplating\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

use const Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;
use const Phpolar\Phpolar\Tests\TEST_GET_ROUTE;
use const Phpolar\Phpolar\Tests\TEST_POST_ROUTE;
use const Phpolar\Phpolar\Tests\FORM_TPL_PATH;
use const Phpolar\Phpolar\Tests\LIST_TPL_PATH;

final class MemoryUsageTest extends TestCase
{
    protected function getContainer(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        TemplateEngine $templateEngine,
        ?RequestHandlerInterface $handler = null,
    ): ContainerInterface {
        $config = new ContainerConfigurationStub();
        if ($handler !== null) {
            $config[RequestHandlerInterface::class] = $handler;
        }
        $config[ContainerInterface::class] = static fn (ArrayAccess $conf) => new ConfigurableContainerStub($conf);
        $config[ResponseFactoryInterface::class] = $responseFactory;
        $config[StreamFactoryInterface::class] = $streamFactory;
        $config[TemplateEngine::class] = $templateEngine;
        $config[MiddlewareProcessingQueue::class] = static fn (ArrayAccess $conf) => new MiddlewareProcessingQueue($conf[ContainerInterface::class]);
        $config[CsrfPreRoutingMiddleware::class] = new CsrfPreRoutingMiddleware($responseFactory, $streamFactory);
        $config[CsrfPostRoutingMiddlewareFactory::class] = new CsrfPostRoutingMiddlewareFactory($responseFactory, $streamFactory);
        $container = new ConfigurableContainerStub($config);
        $config[WebServer::ERROR_HANDLER_401] = static fn (ArrayAccess $conf) => new ErrorHandler(401, "Unauthorized", $conf[ContainerInterface::class]);
        return $container;
    }

    #[Test]
    #[TestDox("Memory usage for a get request shall be below " . PROJECT_MEMORY_USAGE_THRESHOLD . " bytes")]
    public function shallBeBelowThreshold1()
    {
        $totalUsed = -memory_get_usage();
        $this->handleGetRequest();
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) PROJECT_MEMORY_USAGE_THRESHOLD, $totalUsed);
    }

    #[Test]
    #[TestDox("Memory usage for a post request shall be below " . PROJECT_MEMORY_USAGE_THRESHOLD . " bytes")]
    public function shallBeBelowThreshold2()
    {
        $totalUsed = -memory_get_usage();
        $this->handlePostRequest();
        $totalUsed += memory_get_usage();
        $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual((int) PROJECT_MEMORY_USAGE_THRESHOLD, $totalUsed);
    }

    private function handleGetRequest(): self
    {
        $this->expectOutputRegex("/<form/");
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $templateEngine = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $routeRegistry = new RouteRegistry();
        $context = new HtmlSafeContext(new FakeModel());
        $routeHandler = new class ($templateEngine, $context) extends AbstractRouteDelegate
        {
            public function __construct(private TemplateEngine $templateEngine, private HtmlSafeContext $context)
            {
            }

            public function handle(ContainerInterface $container): string
            {
                return $this->templateEngine->apply(
                    FORM_TPL_PATH,
                    $this->context,
                );
            }
        };
        $requestHandler = new class ($responseFactory, $streamFactory, $routeHandler) implements RequestHandlerInterface
        {
            public function __construct(
                private ResponseFactoryInterface $responseFactory,
                private StreamFactoryInterface $streamFactroy,
                private AbstractRouteDelegate $routeHandler
            ) {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->responseFactory->createResponse()
                    ->withBody($this->streamFactroy->createStream($this->routeHandler->handle(new ConfigurableContainerStub(new ContainerConfigurationStub()))));
            }
        };
        $routeRegistry->addGet(TEST_GET_ROUTE, $routeHandler);
        $container = $this->getContainer(
            $responseFactory,
            $streamFactory,
            $templateEngine,
            $requestHandler,
        );
        $app = WebServer::createApp(new ContainerFactory(static fn () => $container), new ContainerConfigurationStub());
        $app->useRoutes($routeRegistry);
        $app->useCsrfMiddleware();
        $app->receive((new RequestStub("GET"))->withUri(new UriStub(TEST_GET_ROUTE)));

        return $this;
    }

    private function handlePostRequest(): self
    {
        $this->expectOutputRegex("/<ul/");
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $templateEngine = new TemplateEngine(new StreamContentStrategy(), new Binder(), new Dispatcher());
        $routeRegistry = new RouteRegistry();
        $routeHandler = new class ($templateEngine) extends AbstractRouteDelegate
        {
            public function __construct(private TemplateEngine $templateEngine)
            {
            }

            public function handle(ContainerInterface $container): string
            {
                $saved = new FakeModel();
                $saved->myInput = "something else";
                $storage = new class () extends AbstractStorage
                {
                    public function commit(): void
                    {
                        // no op
                    }
                    public function load(): void
                    {
                        // no op
                    }
                };
                $key = new ItemKey(uniqid());
                $storage->storeByKey($key, new Item($saved));
                $modelList = new ModelList();
                $modelList->add($saved);
                return $this->templateEngine->apply(
                    LIST_TPL_PATH,
                    new HtmlSafeContext($modelList)
                );
            }
        };
        $routeRegistry->addPost(TEST_POST_ROUTE, $routeHandler);
        $container = $this->getContainer(
            $responseFactory,
            $streamFactory,
            $templateEngine,
        );
        $app = WebServer::createApp(new ContainerFactory(static fn () => $container), new ContainerConfigurationStub());
        $app->useRoutes($routeRegistry);
        $app->useCsrfMiddleware();
        $app->receive((new RequestStub("POST"))->withUri(new UriStub(TEST_POST_ROUTE)));

        return $this;
    }
}
