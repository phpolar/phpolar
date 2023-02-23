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
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;

return [
    Binder::class => new Binder(),
    Dispatcher::class => new Dispatcher(),
    WebServer::ERROR_HANDLER_401 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $config[ContainerInterface::class],
    ),
    MiddlewareProcessingQueue::class => static fn (ArrayAccess $config) => new MiddlewareProcessingQueue($config[ContainerInterface::class]),
    TemplateEngine::class => static fn (ArrayAccess $config) =>  new TemplateEngine(
        $config[TemplatingStrategyInterface::class],
        $config[Binder::class],
        $config[Dispatcher::class],
    ),
];
