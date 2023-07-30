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
use Phpolar\Phpolar\Http\RouteRegistry;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
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
