<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageFactory;
use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;

use Closure;

/**
 * Integrates the libraries features
 * into an application.
 */
final class App
{
    /**
     * @var string
     */
    public const ROOT_PATH = "/";

    /**
     * @var string
     */
    public const FAVICON_PATH = "/favicon.ico";

    private CollectionStorageInterface $storage;

    private TemplateContext $page;

    private string $requestUri;

    private AppConfigInterface $appConfig;

    /**
     * @var array<string,Closure>
     */
    private array $routeMap;

    private function __construct(
        string $requestUri,
        AppConfigInterface $appConfig
    ) {
        $this->requestUri = $requestUri;
        $this->appConfig = $appConfig;
    }

    /**
     * Creates an app object using
     * the provided configuration.
     */
    public static function configure(
        string $requestUri,
        AppConfigInterface $appConfig
    ): self {
        return new self($requestUri, $appConfig);
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
        if (isset($this->routeMap[$this->requestUri]) === true)
        {
            $this->routeMap[$this->requestUri]($this->page, $this->storage);
        }
        else
        {
            static::notFoundHandler();
        }
    }
}
