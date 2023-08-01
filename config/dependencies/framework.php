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

use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Authenticator\AuthenticatorInterface;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Phpolar\Http\RouteMap;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingHandler::class => static fn (ContainerInterface $container) => new RoutingHandler(
        routeRegistry: $container->get(RouteMap::class),
        responseFactory: $container->get(ResponseFactoryInterface::class),
        streamFactory: $container->get(StreamFactoryInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
        authChecker: $container->get(DiTokens::NOOP_AUTH_CHECKER),
        container: $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::NOOP_AUTH_CHECKER => static fn (ContainerInterface $container) => new AuthorizationChecker(
        routableResolver: new class () implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface|false
            {
                // authorized by default
                return $target;
            }
        },
        unauthHandler: new class ($container->get(ResponseFactoryInterface::class)) implements RequestHandlerInterface {
            public function __construct(private ResponseFactoryInterface $responseFactory)
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->responseFactory->createResponse(ResponseCode::UNAUTHORIZED, "Unauthorized");
            }
        },
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    AuthorizationChecker::class => static fn (ContainerInterface $container) => new AuthorizationChecker(
        routableResolver: new ProtectedRoutableResolver($container->get(AuthenticatorInterface::class)),
        unauthHandler: $container->get(DiTokens::UNAUTHORIZED_HANDLER),
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::AUTHENTICATED_ROUTING_HANDLER => static fn (ContainerInterface $container) => new RoutingHandler(
        routeRegistry: $container->get(RouteMap::class),
        responseFactory: $container->get(ResponseFactoryInterface::class),
        streamFactory: $container->get(StreamFactoryInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
        authChecker: $container->get(AuthorizationChecker::class),
        container: $container,
    ),
    /**
     * @suppress PhanUnreferencedClosure
     */
    MiddlewareQueueRequestHandler::class => static function (ContainerInterface $container) {
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $fallbackHandler = new class ($responseFactory) implements RequestHandlerInterface {
            public function __construct(private ResponseFactoryInterface $responseFactory)
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->responseFactory->createResponse(ResponseCode::NOT_FOUND, "Not Found");
            }
        };
        return new MiddlewareQueueRequestHandler($fallbackHandler);
    },
    /**
     * @suppress PhanUnreferencedClosure
     */
    RoutingMiddleware::class => static fn (ContainerInterface $container) => new RoutingMiddleware($container->get(RoutingHandler::class)),
    /**
     * @suppress PhanUnreferencedClosure
     */
    DiTokens::RESPONSE_EMITTER => new Laminas\HttpHandlerRunner\Emitter\SapiEmitter(),
];
