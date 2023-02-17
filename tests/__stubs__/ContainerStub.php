<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use ArrayAccess;
use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Phpolar\Routing\AbstractRouteDelegate;
use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Phpolar\Phpolar\WebServer\MiddlewareProcessingQueue;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\PhpTemplating\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ContainerStub implements ContainerInterface, ArrayAccess
{
    /**
     * @var array<string,object>
     */
    private static array $deps = [];

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Error401Handler $error401Handler,
        TemplateEngine $templateEngine,
        MiddlewareProcessingQueue $middlewareProcessingQueue,
        ?CsrfPreRoutingMiddleware $csrfPreRoutingMiddleware = null,
        ?CsrfPostRoutingMiddlewareFactory $csrfPostRoutingMiddlewareFactory = null,
        ?RequestHandlerInterface $handler = null,
    ) {
        self::$deps[WebServer::PRIMARY_REQUEST_HANDLER] = $handler;
        self::$deps[ResponseFactoryInterface::class] = $responseFactory;
        self::$deps[StreamFactoryInterface::class] = $streamFactory;
        self::$deps[Error401Handler::class] = $error401Handler;
        self::$deps[TemplateEngine::class] = $templateEngine;
        self::$deps[MiddlewareProcessingQueue::class] = $middlewareProcessingQueue;
        self::$deps[CsrfPreRoutingMiddleware::class] = $csrfPreRoutingMiddleware ?? new CsrfPreRoutingMiddleware($responseFactory, $streamFactory);
        self::$deps[CsrfPostRoutingMiddlewareFactory::class] = $csrfPostRoutingMiddlewareFactory ?? new CsrfPostRoutingMiddlewareFactory($responseFactory, $streamFactory);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, self::$deps);
    }

    public function get(string $id)
    {
        return self::$deps[$id];
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
