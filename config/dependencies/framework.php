<?php

/**
 * This file contains all services/dependencies required
 * by the framework.  Setting up the dependencies when
 * the framework is bootstrapped allows its users
 * not to have to worry about it.
 *
 * The framework uses any PSR-11 container for
 * interoperability with other frameworks and to allow
 * users to use whatever implementation they want.
 * @phan-file-suppress PhanUnreferencedClosure
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */

declare(strict_types=1);

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Phpolar\ModelResolver\ModelResolverInterface;
use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\Phpolar\Auth\ProtectedRoutableResolver;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Phpolar\Http\RequestProcessingHandler;
use Phpolar\Phpolar\Http\RequestProcessorExecutor;
use Phpolar\Phpolar\Http\ResponseBuilder;
use Phpolar\Phpolar\Http\ServerInterface;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    RequestProcessorExecutor::class => new RequestProcessorExecutor(),
    RoutableResolverInterface::class => static fn(ContainerInterface $container) => new ProtectedRoutableResolver($container->get(AuthenticatorInterface::class)),
    DiTokens::RESPONSE_EMITTER => new Laminas\HttpHandlerRunner\Emitter\SapiEmitter(),
    DiTokens::NOOP_ROUTABLE_RESOLVER => new class() implements RoutableResolverInterface {
        public function resolve(RoutableInterface $target): RoutableInterface|false
        {
            // authorized by default
            return $target;
        }
    },
    MiddlewareQueueRequestHandler::class => static function (ContainerInterface $container) {
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        $fallbackHandler = new class($responseFactory) implements RequestHandlerInterface {
            public function __construct(private ResponseFactoryInterface $responseFactory) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->responseFactory->createResponse((int) HttpResponseCode::NotFound->value, HttpResponseCode::NotFound->getLabel());
            }
        };
        return new MiddlewareQueueRequestHandler($fallbackHandler);
    },
    DiTokens::NOOP_AUTH_CHECKER => static fn(ContainerInterface $container) => new AuthorizationChecker(
        routableResolver: $container->get(DiTokens::NOOP_ROUTABLE_RESOLVER),
        unauthHandler: $container->get(DiTokens::UNAUTHORIZED_HANDLER),
    ),
    DiTokens::AUTHENTICATED_ROUTING_HANDLER => static fn(ContainerInterface $container) => new RequestProcessingHandler(
        server: $container->get(ServerInterface::class),
        processorExecutor: $container->get(RequestProcessorExecutor::class),
        authChecker: $container->get(AuthorizationChecker::class),
        responseBuilder: $container->get(ResponseBuilder::class),
        propertyInjector: $container->get(PropertyInjectorInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
    ),
    DiTokens::UNAUTHORIZED_HANDLER => static fn(ContainerInterface $container) => new class($container->get(ResponseFactoryInterface::class)) implements RequestHandlerInterface {
        public function __construct(private ResponseFactoryInterface $responseFactory) {}

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return $this->responseFactory->createResponse((int) HttpResponseCode::Unauthorized->value, HttpResponseCode::Unauthorized->getLabel());
        }
    },
    DiTokens::NOOP_PROPERTY_INJECTOR => new class() implements PropertyInjectorInterface {
        public function inject(object $injectee): void
        {
            // intentionally empty
        }
    },
    ResponseBuilder::class => static fn(ContainerInterface $container) => new ResponseBuilder(
        responseFactory: $container->get(ResponseFactoryInterface::class),
        streamFactory: $container->get(StreamFactoryInterface::class),
    ),
    RequestProcessingHandler::class => static fn(ContainerInterface $container) => new RequestProcessingHandler(
        server: $container->get(ServerInterface::class),
        processorExecutor: $container->get(RequestProcessorExecutor::class),
        responseBuilder: $container->get(ResponseBuilder::class),
        authChecker: $container->get(DiTokens::NOOP_AUTH_CHECKER),
        propertyInjector: $container->get(PropertyInjectorInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
    ),
    AuthorizationChecker::class => static fn(ContainerInterface $container) => new AuthorizationChecker(
        routableResolver: $container->get(RoutableResolverInterface::class),
        unauthHandler: $container->get(DiTokens::UNAUTHORIZED_HANDLER),
    ),
    RoutingMiddleware::class => static fn(ContainerInterface $container) => new RoutingMiddleware($container->get(RequestProcessingHandler::class)),
];
