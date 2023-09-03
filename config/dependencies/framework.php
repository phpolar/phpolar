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
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Auth\AuthenticatorInterface;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Phpolar\Http\RouteRegistry;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\RoutableResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::ERROR_HANDLER_401 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::ERROR_HANDLER_404 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingHandler::class => static fn (ContainerInterface $container) => new RoutingHandler(
        routeRegistry: $container->get(RouteRegistry::class),
        responseFactory: $container->get(ResponseFactoryInterface::class),
        streamFactory: $container->get(StreamFactoryInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
        container: $container,
        routableResolver: $container->get(RoutableResolverInterface::class),
        unauthHandler: $container->get(DiTokens::UNAUTHORIZED_HANDLER),
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutableResolverInterface::class => static fn (ContainerInterface $container) => new ProtectedRoutableResolver($container->get(AuthenticatorInterface::class)),
    /**
     * @suppress PhanUnreferencedClosure
     */
    MiddlewareQueueRequestHandler::class => static fn (ContainerInterface $container) => new MiddlewareQueueRequestHandler($container->get(DiTokens::ERROR_HANDLER_404)),
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingMiddleware::class => static fn (ContainerInterface $container) => new RoutingMiddleware($container->get(RoutingHandler::class)),
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::RESPONSE_EMITTER => new Laminas\HttpHandlerRunner\Emitter\SapiEmitter(),
];
