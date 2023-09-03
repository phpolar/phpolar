<?php

/**
 * This file contains all services/dependencies required
 * by the framework.  Setting up the dependencies when
 * the framework is bootstrapped allows its users
 * not to have to worry about it.
 *
 * The framework is any PSR-11 container for
 * interoperability with other frameworks and to allow
 * users to use whatever implementation they want.
 */

declare(strict_types=1);

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\WebServer\MiddlewareProcessingQueue;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\TemplateEngine;
use Phpolar\PhpTemplating\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;

return [
    Binder::class => new Binder(),
    Dispatcher::class => new Dispatcher(),
    WebServer::ERROR_HANDLER_401 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $container,
    ),
    WebServer::ERROR_HANDLER_404 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $container,
    ),
    MiddlewareProcessingQueue::class => static fn (ContainerInterface $container) => new MiddlewareProcessingQueue($container),
    TemplateEngine::class => static fn (ContainerInterface $container) =>  new TemplateEngine(
        $container->get(TemplatingStrategyInterface::class),
        $container->get(Binder::class),
        $container->get(Dispatcher::class),
    ),
];
