<?php

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    PrimaryHandler::class => static fn (ArrayAccess $config) => new PrimaryHandler($config[WebServer::ERROR_HANDLER_404]),
    WebServer::ERROR_HANDLER_401 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $config[ContainerInterface::class],
    ),
    WebServer::ERROR_HANDLER_404 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::NOT_FOUND,
        "Not Found",
        $config[ContainerInterface::class],
    ),
    TemplateEngine::class => static fn (ArrayAccess $config) => new TemplateEngine($config[TemplatingStrategyInterface::class], new Binder(), new Dispatcher()),
    ContainerInterface::class => static fn (ArrayAccess $config) => new ConfigurableContainerStub($config),
    TemplatingStrategyInterface::class => new StreamContentStrategy(),
    ResponseFactoryInterface::class => new ResponseFactoryStub(),
    StreamFactoryInterface::class => new StreamFactoryStub(),
];
