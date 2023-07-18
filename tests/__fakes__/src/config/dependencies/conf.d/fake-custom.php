<?php

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Auth\AuthenticatorInterface;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\RoutableInterface;
use Phpolar\Phpolar\Http\RouteRegistry;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\RoutableResolverInterface;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    MiddlewareQueueRequestHandler::class => static fn (ContainerInterface $container) => new MiddlewareQueueRequestHandler($container->get(DiTokens::ERROR_HANDLER_404)),
    DiTokens::ERROR_HANDLER_401 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $container,
    ),
    DiTokens::ERROR_HANDLER_404 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $container,
    ),
    TemplateEngine::class => static fn (ContainerInterface $container) => new TemplateEngine($container->get(TemplatingStrategyInterface::class), new Binder(), new Dispatcher()),
    TemplatingStrategyInterface::class => new StreamContentStrategy(),
    ResponseFactoryInterface::class => new ResponseFactoryStub(),
    StreamFactoryInterface::class => new StreamFactoryStub("+w"),
    RouteRegistry::class => new RouteRegistry(),
    DiTokens::RESPONSE_EMITTER => new SapiEmitter(),
    AuthenticatorInterface::class => new class () implements AuthenticatorInterface {
        public function getCredentials(): ?object
        {
            return null;
        }
    },
    RoutingMiddleware::class => static fn (ContainerInterface $container) => new RoutingMiddleware($container->get(RoutingHandler::class)),
    RoutingHandler::class => static fn (ContainerInterface $container) => new RoutingHandler(
        $container->get(RouteRegistry::class),
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        $container,
        new class () implements ModelResolverInterface {
            public function resolve(object $it, string $methodName): array
            {
                return [];
            }
        },
        new class () implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface | false
            {
                return $target;
            }
        },
    )
];
