<?php

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversClassesThatImplementInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlRepresentation::class)]
#[CoversClassesThatImplementInterface(RepresentationInterface::class)]
#[CoversClass(InvalidHtmlResponseException::class)]
final class HtmlRepresentationTest extends TestCase
{
    #[TestDox("Shall return the result of the decorated request processor if the return value is a string")]
    #[TestWith(["<h1>Fake Title</h1>"])]
    public function testa(string $expectedResult)
    {
        $sut = new HtmlRepresentation($expectedResult);

        $result = (string) $sut;

        $this->assertSame($expectedResult, $result);
    }

    #[TestDox("Shall throw an InvalidHtmlResponseException if the decorated request processor does not return a string")]
    #[TestWith([null])]
    #[TestWith([true])]
    #[TestWith([[]])]
    public function testc(null | true | array $invalidResult)
    {
        $this->expectException(InvalidHtmlResponseException::class);

        new HtmlRepresentation($invalidResult);
    }

    #[TestDox("Shall return its mime type")]
    public function testd()
    {
        $sut = new HtmlRepresentation("");

        $result = $sut->getMimeType();

        $this->assertSame(MimeType::TextHtml->value, $result);
    }
}
