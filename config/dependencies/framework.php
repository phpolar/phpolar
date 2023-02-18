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

use Phpolar\Phpolar\WebServer\Http\Error401Handler;
use Phpolar\Phpolar\WebServer\MiddlewareProcessingQueue;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\TemplateEngine;
use Phpolar\PhpTemplating\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    Binder::class => new Binder(),
    Dispatcher::class => new Dispatcher(),
    Error401Handler::class => static fn (ContainerInterface $container) => new Error401Handler(
        $container->get(ResponseFactoryInterface::class),
        $container->get(StreamFactoryInterface::class),
        $container->get(TemplateEngine::class),
    ),
    MiddlewareProcessingQueue::class => static fn () => new MiddlewareProcessingQueue(),
    TemplateEngine::class => static fn (ContainerInterface $container) =>  new TemplateEngine(
        $container->get(TemplatingStrategyInterface::class),
        $container->get(Binder::class),
        $container->get(Dispatcher::class),
    ),
];
