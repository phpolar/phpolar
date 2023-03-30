<?php

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\Http\ErrorHandler;
use Phpolar\Phpolar\Http\PrimaryHandler;
use Phpolar\Phpolar\App;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    PrimaryHandler::class => static fn (ContainerInterface $container) => new PrimaryHandler($container->get(App::ERROR_HANDLER_404)),
    App::ERROR_HANDLER_401 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $container,
    ),
    App::ERROR_HANDLER_404 => static fn (ContainerInterface $container) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $container,
    ),
    TemplateEngine::class => static fn (ContainerInterface $container) => new TemplateEngine($container->get(TemplatingStrategyInterface::class), new Binder(), new Dispatcher()),
    TemplatingStrategyInterface::class => new StreamContentStrategy(),
    ResponseFactoryInterface::class => new ResponseFactoryStub(),
    StreamFactoryInterface::class => new StreamFactoryStub(),
];
