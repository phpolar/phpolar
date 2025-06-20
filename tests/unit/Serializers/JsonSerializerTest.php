<?php

namespace Phpolar\Phpolar\Serializers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonSerializer::class)]
final class JsonSerializerTest extends TestCase
{
    #[TestDox("Shall produce a JSON representation of scalar values")]
    #[TestWith(["ANY_STRING", "\"ANY_STRING\""])]
    #[TestWith([1, "1"])]
    #[TestWith([true, "true"])]
    #[TestWith([false, "false"])]
    #[TestWith([null, "null"])]
    public function testa(string|int|bool|null $givenData, $expected)
    {
        $sut = new JsonSerializer();
        $result = $sut->serialize($givenData);
        $this->assertSame($expected, $result);
    }

    #[TestDox("Shall produce a JSON representation of an array")]
    #[TestWith([[1, "ANY_STRING"], "[1,\"ANY_STRING\"]"])]
    public function testb(array $givenData, $expected)
    {
        $sut = new JsonSerializer();
        $result = $sut->serialize($givenData);
        $this->assertSame($expected, $result);
    }

    #[TestDox("Shall produce a JSON representation of an object")]
    public function testc()
    {
        $givenData = (object) ["name" => "Someone", "address" => "Somewhere, KS"];
        $expected = "{\"name\":\"Someone\",\"address\":\"Somewhere, KS\"}";
        $sut = new JsonSerializer();
        $result = $sut->serialize($givenData);
        $this->assertSame($expected, $result);
    }

    #[TestDox("Shall call the given failure handler when encoding fails")]
    public function testd()
    {
        $givenData = (object) ["it" => (object) ["it" => (object) ["it" => ""]]];
        $expected = "FAILURE IS AN OPTION";
        $failureHandler = static fn($data) => $expected;
        $sut = new JsonSerializer(depth: 1, failureHandler: $failureHandler);
        $result = $sut->serialize($givenData);
        $this->assertSame($expected, $result);
    }

    #[TestDox("Shall return an empty string when not failure handler is provided and encoding fails")]
    public function teste()
    {
        $givenData = (object) ["it" => (object) ["it" => (object) ["it" => ""]]];
        $expected = "";
        $sut = new JsonSerializer(flags: JSON_FORCE_OBJECT, depth: 2);
        $result = $sut->serialize($givenData);
        $this->assertSame($expected, $result);
    }
}
