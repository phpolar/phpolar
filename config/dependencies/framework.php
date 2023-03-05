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
use Phpolar\Phpolar\Routing\RouteRegistry;
use Phpolar\Phpolar\Routing\RoutingHandler;
use Phpolar\Phpolar\Routing\RoutingMiddleware;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Phpolar\Phpolar\WebServer\WebServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    /**
     * @suppress PhanUnreferencedClosure
     */
    WebServer::ERROR_HANDLER_401 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $config[ContainerInterface::class],
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    WebServer::ERROR_HANDLER_404 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $config[ContainerInterface::class],
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingHandler::class => static fn (ArrayAccess $config) => new RoutingHandler(
        $config[RouteRegistry::class],
        $config[ResponseFactoryInterface::class],
        $config[StreamFactoryInterface::class],
        $config[WebServer::ERROR_HANDLER_404],
        $config[ContainerInterface::class],
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    PrimaryHandler::class => static fn (ArrayAccess $config) => new PrimaryHandler($config[WebServer::ERROR_HANDLER_404]),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingMiddleware::class => static fn (ArrayAccess $config) => new RoutingMiddleware($config[RoutingHandler::class])
];
