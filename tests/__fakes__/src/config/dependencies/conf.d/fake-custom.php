<?php

declare(strict_types=1);

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\ModelResolver\ModelResolverInterface;
use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use Phpolar\Model\ParsedBodyResolver;
use Phpolar\Phpolar\Http\MiddlewareQueueRequestHandler;
use Phpolar\Phpolar\DependencyInjection\DiTokens;
use Phpolar\Phpolar\Http\AuthorizationChecker;
use Phpolar\Phpolar\Http\RequestProcessingHandler;
use Phpolar\Phpolar\Http\RequestProcessorExecutor;
use Phpolar\Phpolar\Http\ResponseBuilder;
use Phpolar\Phpolar\Http\RoutingMiddleware;
use Phpolar\Phpolar\Http\Server;
use Phpolar\Phpolar\Http\ServerInterface;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    ModelResolverInterface::class => new ParsedBodyResolver($_REQUEST),
    MiddlewareQueueRequestHandler::class => new MiddlewareQueueRequestHandler(
        new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseFactoryStub((new StreamFactoryStub("+w"))->createStream()))->createResponse(404);
            }
        }
    ),
    TemplateEngine::class => static fn() => new TemplateEngine(),
    ResponseFactoryInterface::class => new ResponseFactoryStub((new StreamFactoryStub("+w"))->createStream()),
    StreamFactoryInterface::class => new StreamFactoryStub("+w"),
    ServerInterface::class => new Server(
        interface: []
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
    RoutingMiddleware::class => static fn(ContainerInterface $container) => new RoutingMiddleware($container->get(RequestProcessingHandler::class)),
    ResponseBuilder::class => static fn(ContainerInterface $container) => new ResponseBuilder(
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
    ),
    RequestProcessingHandler::class => static fn(ContainerInterface $container) => new RequestProcessingHandler(
        server: $container->get(ServerInterface::class),
        processorExecutor: $container->get(RequestProcessorExecutor::class),
        responseBuilder: $container->get(ResponseBuilder::class),
        authChecker: $container->get(AuthorizationChecker::class),
        propertyInjector: $container->get(PropertyInjectorInterface::class),
        modelResolver: $container->get(ModelResolverInterface::class),
    ),
    RequestProcessorExecutor::class => new RequestProcessorExecutor(),
    AuthorizationChecker::class => static fn() => new AuthorizationChecker(
        new class () implements RequestProcessorResolverInterface {
            public function resolve(RequestProcessorInterface $target): RequestProcessorInterface | false
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
