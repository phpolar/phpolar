<?php

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\MemoryStreamStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Phpolar\Phpolar\WebServer\Http\PrimaryHandler;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\Phpolar\WebServer\WebServerTest;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\StreamContentStrategy;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    PrimaryHandler::class => static fn (ArrayAccess $config) => new class ($config[WebServer::ERROR_HANDLER_401]) extends PrimaryHandler {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return (new ResponseStub())->withBody(new MemoryStreamStub(WebServerTest::RESPONSE_CONTENT));
        }
    },
    WebServer::ERROR_HANDLER_401 => static fn (ArrayAccess $config) => new ErrorHandler(
        ResponseCode::UNAUTHORIZED,
        "Unauthorized",
        $config[ContainerInterface::class],
    ),
    TemplateEngine::class => static fn (ArrayAccess $config) => new TemplateEngine($config[TemplatingStrategyInterface::class], new Binder(), new Dispatcher()),
    ContainerInterface::class => static fn (ArrayAccess $config) => new ConfigurableContainerStub($config),
    TemplatingStrategyInterface::class => new StreamContentStrategy(),
    ResponseFactoryInterface::class => new ResponseFactoryStub(),
    StreamFactoryInterface::class => new StreamFactoryStub(),
];
