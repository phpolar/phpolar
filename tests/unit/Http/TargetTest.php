<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Target::class)]
final class TargetTest extends TestCase
{
    #[TestDox("Shall return \"not acceptable\" if the Accept header does not contain a configured representation")]
    #[TestWith([[MimeType::ApplicationJson->value], [MimeType::ApplicationEpubZip]])]
    public function testa(array $acceptableRepresentations, array $representations)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub
            ->method("getHeader")
            ->willReturn($acceptableRepresentations);

        $sut = new Target(
            "",
            HttpMethod::Get,
            new Representations($representations),
            $requestProcessorStub,
        );

        $result = $sut->negotiate($requestStub);

        $this->assertSame(HttpResponseCode::NotAcceptable->value, $result->value);
    }

    #[TestDox("Shall return \"not acceptable\" if the configured representation are not supported")]
    #[TestWith([[MimeType::ApplicationEpubZip->value], [MimeType::ApplicationEpubZip]])]
    public function testb(array $acceptableRepresentations, array $representations)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub
            ->method("getHeader")
            ->willReturn($acceptableRepresentations);

        $sut = new Target(
            "",
            HttpMethod::Get,
            new Representations($representations),
            $requestProcessorStub,
        );

        $result = $sut->negotiate($requestStub);

        $this->assertSame(HttpResponseCode::NotAcceptable->value, $result->value);
    }

    #[TestDox("Shall return \"created\" if the method is post")]
    #[TestWith([HttpMethod::Post, [MimeType::ApplicationJson->value], [MimeType::ApplicationJson]])]
    public function testc(HttpMethod $httpMethod, array $acceptableRepresentations, array $representations)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub
            ->method("getHeader")
            ->willReturn($acceptableRepresentations);
        $requestStub
            ->method("getMethod")
            ->willReturn($httpMethod->value);

        $sut = new Target(
            "",
            $httpMethod,
            new Representations($representations),
            $requestProcessorStub,
        );

        $result = $sut->negotiate($requestStub);

        $this->assertSame(HttpResponseCode::Created->value, $result->value);
    }

    #[TestDox("Shall return \"OK\" if the method is GET")]
    #[TestWith([HttpMethod::Get, [MimeType::ApplicationJson->value], [MimeType::ApplicationJson]])]
    public function testcb(HttpMethod $httpMethod, array $acceptableRepresentations, array $representations)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub
            ->method("getHeader")
            ->willReturn($acceptableRepresentations);
        $requestStub
            ->method("getMethod")
            ->willReturn($httpMethod->value);

        $sut = new Target(
            "",
            $httpMethod,
            new Representations($representations),
            $requestProcessorStub,
        );

        $result = $sut->negotiate($requestStub);

        $this->assertSame(HttpResponseCode::Ok->value, $result->value);
    }

    #[TestWith(["/", "html", HttpMethod::Get, MimeType::TextHtml, "<h1>what</h1>", "<h1>what</h1>"])]
    #[TestDox('Shall return return the $type representation if given representations include $mimeType')]
    public function testd(string $location, string $type, HttpMethod $method, MimeType $mimeType, string $resource, string $expected)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);

        $sut = new Target(
            location: $location,
            method: $method,
            representations: new Representations([$mimeType, MimeType::TextPlain]),
            requestProcessor: $requestProcessorStub,
        );

        $result = (string) $sut->getRepresentation($resource);

        $this->assertSame($expected, $result);
    }

    #[TestWith(["/", "JSON", HttpMethod::Get, MimeType::ApplicationJson, ["some string"], "[\"some string\"]"])]
    #[TestDox('Shall return return the $type representation if given representations include $mimeType')]
    public function teste(string $location, string $type, HttpMethod $method, MimeType $mimeType, mixed $resource, string $expected)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);

        $sut = new Target(
            location: $location,
            method: $method,
            representations: new Representations([$mimeType, MimeType::TextPlain]),
            requestProcessor: $requestProcessorStub,
        );

        $result = (string) $sut->getRepresentation($resource);

        $this->assertSame($expected, $result);
    }

    #[TestWith(["/", "html", HttpMethod::Get, MimeType::ApplicationEpubZip, "<h1>title</h1>", "<h1>title</h1>"])]
    #[TestDox('Shall return return the $type representation if given representations include $mimeType')]
    public function testf(string $location, string $type, HttpMethod $method, MimeType $mimeType, mixed $resource, string $expected)
    {
        $requestProcessorStub = $this->createStub(RoutableInterface::class);

        $sut = new Target(
            location: $location,
            method: $method,
            representations: new Representations([$mimeType, MimeType::TextPlain]),
            requestProcessor: $requestProcessorStub,
        );

        $result = (string) $sut->getRepresentation($resource);

        $this->assertSame($expected, $result);
    }

    #[TestDox("Shall know if location matches")]
    #[TestWith(["/", "/"])]
    #[TestWith(["/path/{id}", "/path/123"])]
    public function testg(string $location, string $requestPath)
    {
        $sut = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([]),
            requestProcessor: $this->createStub(RoutableInterface::class),
        );

        $result = $sut->matchesLocation($requestPath);

        $this->assertTrue($result);
    }

    #[TestDox("Shall know if location does not match")]
    #[TestWith(["/", "/not/there"])]
    #[TestWith(["/path/{id}", "/path/nope/123"])]
    public function testh(string $location, string $requestPath)
    {
        $sut = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([]),
            requestProcessor: $this->createStub(RoutableInterface::class),
        );

        $result = $sut->matchesLocation($requestPath);

        $this->assertFalse($result);
    }
}
