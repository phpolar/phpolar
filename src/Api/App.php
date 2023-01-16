<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageFactory;
use Phpolar\Phpolar\Api\Rendering\TemplateContext;
use Phpolar\Phpolar\Api\DataStorage\CollectionStorageInterface;

use Closure;
use Phpolar\Phpolar\Stock\AppConfig\InMemoryAppConfig;
use Phpolar\Phpolar\Stock\DataStorage\CsvFileStorage;

/**
 * Integrates the libraries features
 * into an application.
 */
final class App
{
    public const ROOT_PATH = "/";

    public const FAVICON_PATH = "/favicon.ico";

    private CollectionStorageInterface $storage;

    private TemplateContext $page;

    private AppConfigInterface $appConfig;

    /**
     * @var array<string,Closure>
     */
    private array $routeMap;

    private function __construct(private readonly string $requestUri)
    {
    }

    /**
     * Creates an app object using
     * default configuration and
     * storage
     */
    public static function create(
        string $requestUri
    ): self {
        $app = new self($requestUri);
        $appConfig = new InMemoryAppConfig();
        $app->appConfig = $appConfig;
        $app->storage = new CsvFileStorage($appConfig->getAll(), date("Ym") . ".csv");
        return $app;
    }

    /**
     * Creates an app object using
     * the provided configuration.
     */
    public static function configure(
        string $requestUri,
        AppConfigInterface $appConfig
    ): self {
        $app = new self($requestUri);
        $app->appConfig = $appConfig;
        return $app;
    }

    private static function notFoundHandler(): void
    {
        http_response_code(404);
        echo "Not Found";
    }

    /**
     * Maps routes to handlers.
     *
     * @param array<string,Closure> $routeMap
     *
     * @api
     */
    public function setUpRoutes($routeMap): self
    {
        $this->routeMap = $routeMap;
        return $this;
    }

    /**
     * Sets up root template.
     *
     * @api
     */
    public function setUpRootTemplate(Closure $factory): self
    {
        $this->page = $factory();
        return $this;
    }

    /**
     * Sets up data storage.
     *
     * @api
     */
    public function setUpStorage(CollectionStorageFactory $storageFactory): self
    {
        $this->storage = $storageFactory->getStorage($this->appConfig->getAll());
        return $this;
    }

    /**
     * Applies the request handler for the
     * request uri in the configuration.
     *
     * @api
     */
    public function run(): void
    {
        if (isset($this->routeMap[$this->requestUri]) === false) {
            static::notFoundHandler();
            return;
        }
        $this->routeMap[$this->requestUri]($this->page, $this->storage);
    }
}
