<?php

use Phpolar\Phpolar\Tests\Stubs\MemoryStreamStub;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseStub;
use Phpolar\Phpolar\WebServer\WebServer;
use Phpolar\Phpolar\WebServer\WebServerTest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    WebServer::PRIMARY_REQUEST_HANDLER => static fn () => new class implements RequestHandlerInterface {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return (new ResponseStub())->withBody(new MemoryStreamStub(WebServerTest::RESPONSE_CONTENT));
        }
    }
];
