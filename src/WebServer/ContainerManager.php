<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use ArrayAccess;
use Closure;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\Phpolar\Config\Globs;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Psr\Container\ContainerInterface;

/**
 * Manages the dependency injection container.
 *
 * Adds dependencies, checks for required dependencies,
 * and handles errors.
 */
final class ContainerManager
{
    /**
     * Dependencies/services required
     * by the web server.
     *
     * @var string[]
     */
    private const REQUIRED_DEPS = [
        MiddlewareProcessingQueue::class,
        Error401Handler::class,
    ];

    /**
     * CSRF dependencies required
     * by the web server.
     *
     * @var string[]
     */
    private const REQUIRED_CSRF_DEPS = [
        CsrfPreRoutingMiddleware::class,
        CsrfPostRoutingMiddlewareFactory::class,
    ];

    /**
     * @param ContainerInterface&ArrayAccess<string,mixed> $container
     */
    public function __construct(private ContainerInterface & ArrayAccess $container)
    {
    }

    /**
     * @param Closure $depFactory
     * @param string $depId
     */
    private function addDependency(Closure $depFactory, string $depId): void
    {
        $this->container[$depId] = $depFactory;
    }

    /**
     * Verify if the container has been
     * configured with required dependencies.
     */
    public function checkRequiredDeps(): void
    {
        $this->checkContainer(self::REQUIRED_DEPS);
    }

    /**
     * Verify if the container has been
     * configured with CSRF mitigation dependencies.
     */
    public function checkRequiredCsrfDeps(): void
    {
        $this->checkContainer(self::REQUIRED_CSRF_DEPS);
    }

    /**
     * @param string[] $depsToCheck
     * @throws WebServerConfigurationException
     */
    private function checkContainer(array $depsToCheck): void
    {
        array_walk(
            $depsToCheck,
            fn (string $dep) => $this->container->has($dep)
                || throw new WebServerConfigurationException(
                    sprintf(
                        "Required dependency %s has not been added to the container.",
                        $dep
                    )
                )
        );
    }

    /**
     * Retrieves the CSRF post-routing middleware.
     */
    public function getCsrfPostRoutingMiddlewareFactory(): CsrfPostRoutingMiddlewareFactory
    {
        /**
         * @var CsrfPostRoutingMiddlewareFactory $factory
         */
        $factory = $this->container->get(CsrfPostRoutingMiddlewareFactory::class);
        return $factory;
    }

    /**
     * Retrieves the CSRF pre-routing middleware.
     */
    public function getCsrfPreRoutingMiddleware(): CsrfPreRoutingMiddleware
    {
        /**
         * @var CsrfPreRoutingMiddleware $middleware
         */
        $middleware = $this->container->get(CsrfPreRoutingMiddleware::class);
        return $middleware;
    }

    public function getErrorHandler(): Error401Handler
    {
        /**
         * @var Error401Handler $handler
         */
        $handler = $this->container->get(Error401Handler::class);
        return $handler;
    }

    /**
     * Add services/dependencies to the provided container.
     */
    public function setUpContainer(): void
    {
        if (file_exists(Globs::FrameworkDeps->value) === true) {
            $frameworkDeps = require Globs::FrameworkDeps->value;
            array_walk(
                $frameworkDeps,
                $this->addDependency(...),
            );
        }
        $globResult = glob(Globs::CustomDeps->value, GLOB_BRACE);
        $customDepConfigs = $globResult === false ? [] : $globResult;
        array_walk(
            $customDepConfigs,
            function (string $filename) {
                $customDepConfs = require_once $filename;
                array_walk(
                    $customDepConfs,
                    $this->addDependency(...),
                );
            },
        );
    }
}
