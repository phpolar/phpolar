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
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\PrimaryHandler;
use Phpolar\Phpolar\WebServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    /**
     * @suppress PhanUnreferencedClosure
     */
    WebServer::ERROR_HANDLER_401 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    WebServer::ERROR_HANDLER_404 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingHandler::class => static fn (ContainerInterface $container) => new RoutingHandler(
        $container->get(RouteRegistry::class),
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        $container->get(WebServer::ERROR_HANDLER_404),
        $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    PrimaryHandler::class => static fn (ContainerInterface $container) => new PrimaryHandler($container->get(WebServer::ERROR_HANDLER_404)),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingMiddleware::class => static fn (ContainerInterface $container) => new RoutingMiddleware($container->get(RoutingHandler::class))
];
