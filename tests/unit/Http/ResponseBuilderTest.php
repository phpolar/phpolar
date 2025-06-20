<?php

namespace Phpolar\Phpolar\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(ResponseBuilder::class)]
final class ResponseBuilderTest extends TestCase
{
    #[TestDox("Shall pass the given string to the createStream method of the stream factory")]
    #[TestWith(["ANY_STRING"])]
    public function testa(string $givenString)
    {
        $responseStub = $this->createStub(ResponseInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $responseFactoryStub = $this->createStub(ResponseFactoryInterface::class);
        $responseStub
            ->method("withBody")
            ->willReturn($responseStub);
        $responseFactoryStub
            ->method("createResponse")
            ->willReturn($responseStub);
        $streamFactoryMock
            ->expects($this->once())
            ->method("createStream")
            ->with($this->identicalTo($givenString))
            ->willReturn($streamStub);
        $sut = new ResponseBuilder($responseFactoryStub, $streamFactoryMock);
        $sut->build($givenString);
    }

    #[TestDox("Shall use the stream created by the stream factory to the response body")]
    public function testb()
    {

        $streamStub = $this->createStub(StreamInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $streamFactoryStub = $this->createStub(StreamFactoryInterface::class);
        $responseFactoryStub = $this->createStub(ResponseFactoryInterface::class);
        $streamFactoryStub
            ->method("createStream")
            ->willReturn($streamStub);
        $responseFactoryStub
            ->method("createResponse")
            ->willReturn($responseMock);
        $responseMock
            ->expects($this->once())
            ->method("withBody")
            ->with($this->identicalTo($streamStub))
            ->willReturn($responseMock);
        $sut = new ResponseBuilder($responseFactoryStub, $streamFactoryStub);
        $sut->build();
    }
}
