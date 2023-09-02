<?php

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Authenticator\AuthenticatorInterface;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Phpolar\Http\RouteMap;
use Phpolar\Phpolar\Http\RoutingHandler;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    MiddlewareQueueRequestHandler::class => new MiddlewareQueueRequestHandler(
        new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseFactoryStub((new StreamFactoryStub("+w"))->createStream()))->createResponse(ResponseCode::NOT_FOUND);
            }
        }
    ),
    TemplateEngine::class => static fn () => new TemplateEngine(),
    ResponseFactoryInterface::class => new ResponseFactoryStub((new StreamFactoryStub("+w"))->createStream()),
    StreamFactoryInterface::class => new StreamFactoryStub("+w"),
    RouteMap::class => new RouteMap(
        new class () implements PropertyInjectorInterface {
            public function inject(object $injectee): void
            {
                // noop
            }
        }
    ),
    DiTokens::RESPONSE_EMITTER => new SapiEmitter(),
    AuthenticatorInterface::class => new class () implements AuthenticatorInterface {
        public function isAuthenticated(): bool
        {
            return false;
        }
        public function getCredentials(): ?object
        {
            return null;
        }
        public function getUser(): ?array
        {
            return null;
        }
    },
    RoutingMiddleware::class => static fn (ContainerInterface $container) => new RoutingMiddleware($container->get(RoutingHandler::class)),
    RoutingHandler::class => static fn (ContainerInterface $container) => new RoutingHandler(
        $container->get(RouteMap::class),
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        new class () implements ModelResolverInterface {
            public function resolve(object $it, string $methodName): array
            {
                return [];
            }
        },
        $container->get(AuthorizationChecker::class),
    ),
    AuthorizationChecker::class => static fn (ContainerInterface $container) => new AuthorizationChecker(
        new class () implements RoutableResolverInterface {
            public function resolve(RoutableInterface $target): RoutableInterface | false
            {
                return $target;
            }
        },
        new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new ResponseStub();
            }
        },
    ),
];
