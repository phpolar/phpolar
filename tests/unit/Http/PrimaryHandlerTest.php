<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(PrimaryHandler::class)]
final class PrimaryHandlerTest extends TestCase
{
    #[TestDox("Shall execute the fallback handler when the queue is exhausted")]
    public function test1()
    {
        $expectedResponseContent = "fallback handler content";
        $fallbackHandler = new class ($expectedResponseContent) implements RequestHandlerInterface {
            public function __construct(private string $responseContent)
            {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseStub())->withBody(new MemoryStreamStub($this->responseContent));
            }
        };
        $sut = new PrimaryHandler($fallbackHandler);
        $response = $sut->handle(new RequestStub("GET", "/anything"));
        $this->assertSame($expectedResponseContent, $response->getBody()->getContents());
    }

    #[TestDox("Shall execute the next provided middleware in the queue")]
    public function test2()
    {
        $expectedResponseContent = "content from middleware";
        $givenMiddleware = new class ($expectedResponseContent) implements MiddlewareInterface {
            public function __construct(private string $responseContent)
            {
            }
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return (new ResponseStub())->withBody(new MemoryStreamStub($this->responseContent));
            }
        };
        $fallbackHandler = new class ("fallback handler content") implements RequestHandlerInterface {
            public function __construct(private string $responseContent)
            {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseStub())->withBody(new MemoryStreamStub($this->responseContent));
            }
        };
        $sut = new PrimaryHandler($fallbackHandler);
        $sut->queue($givenMiddleware);
        $response = $sut->handle(new RequestStub("POST", "/anything"));
        $this->assertSame($expectedResponseContent, $response->getBody()->getContents());
    }
}
