<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\Phpolar\Http\Representations;
use Phpolar\Phpolar\Http\Server;
use Phpolar\Phpolar\Http\ServerInterface;
use Phpolar\Phpolar\Http\Target;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversClassesThatImplementInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(Server::class)]
#[CoversClassesThatImplementInterface(ServerInterface::class)]
#[UsesClass(Target::class)]
#[UsesClass(Representations::class)]
final class ServerTest extends TestCase
{
    #[TestWith([HttpMethod::Get, "GET", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([HttpMethod::Get, "GET", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Get, "GET", "", ""])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path", "/some/path"])]
    #[TestWith([HttpMethod::Delete, "DELETE", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/{name}", "/some/path/abcdefg"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/{id}/something", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([HttpMethod::Post, "POST", "/{id}", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall match a route with params to the correct handler with parsed route params. \$location matched \$givenRequestPath")]
    public function testa(HttpMethod $method, string $methodString, string $location, string $givenRequestPath)
    {
        $handlerStub = $this->createStub(RequestProcessorInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);

        $uriStub
            ->method("getPath")
            ->willReturn($givenRequestPath);
        $requestStub
            ->method("getMethod")
            ->willReturn($methodString);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);

        $sut = new Server(
            interface: [
                new Target(
                    location: $location,
                    method: $method,
                    representations: new Representations(
                        [
                            MimeType::TextHtml,
                        ]
                    ),
                    requestProcessor: $handlerStub,
                ),
            ],
        );

        $result = $sut->findTarget($requestStub);

        $this->assertNotSame(HttpResponseCode::NotFound, $result, "Returned not found");
    }

    #[TestWith([HttpMethod::Get, "GET", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([HttpMethod::Get, "GET", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith([HttpMethod::Get, "GET", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Get, "GET", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Get, "GET", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/non-matching-path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/that/does/not/match/{name}", "/some/path/abcdefg"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/{id}/something-else", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/something"])]
    #[TestWith([HttpMethod::Post, "POST", "/some/path/{id}", "/some/path/67a8c963-a381-462d-9530-c2e6beb27a28/an-extra-part"])]
    #[TestWith([HttpMethod::Post, "POST", "", "67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Post, "POST", "", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestWith([HttpMethod::Post, "POST", "/", "/67a8c963-a381-462d-9530-c2e6beb27a28"])]
    #[TestDox("Shall not match a route with params when the path is not a complete match. \$location did not match \$givenRequestPath")]
    public function testb(HttpMethod $method, string $methodString, string $location, string $givenRequestPath)
    {
        $handlerStub = $this->createStub(RequestProcessorInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);

        $uriStub
            ->method("getPath")
            ->willReturn($givenRequestPath);
        $requestStub
            ->method("getMethod")
            ->willReturn($methodString);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);

        $sut = new Server(
            interface: [
                new Target(
                    location: $location,
                    method: $method,
                    representations: new Representations(
                        [
                            MimeType::TextHtml,
                        ]
                    ),
                    requestProcessor: $handlerStub,
                ),
            ],
        );

        $result = $sut->findTarget($requestStub);

        $this->assertSame(HttpResponseCode::NotFound, $result, "Returned not found");
    }

    #[TestWith([HttpMethod::Get, "POST", "/some/path", "/some/path"])]
    #[TestWith([HttpMethod::Delete, "GET", "/some/path/{id}", "/some/path/123"])]
    #[TestWith([HttpMethod::Post, "DELETE", "/some/path/{id}", "/some/path/123"])]
    #[TestDox("Shall return \"method not allowed\" when the request method is not configured on the target. \$location did not match \$givenRequestPath")]
    public function testc(HttpMethod $method, string $methodString, string $location, string $givenRequestPath)
    {
        $handlerStub = $this->createStub(RequestProcessorInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);

        $uriStub
            ->method("getPath")
            ->willReturn($givenRequestPath);
        $requestStub
            ->method("getMethod")
            ->willReturn($methodString);
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);

        $sut = new Server(
            interface: [
                new Target(
                    location: $location,
                    method: $method,
                    representations: new Representations(
                        [
                            MimeType::TextHtml,
                        ]
                    ),
                    requestProcessor: $handlerStub,
                ),
            ],
        );

        $result = $sut->findTarget($requestStub);

        $this->assertSame(HttpResponseCode::MethodNotAllowed, $result, "Method not allowed");
    }
}
