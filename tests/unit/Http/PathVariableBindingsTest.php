<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(PathVariableBindings::class)]
final class PathVariableBindingsTest extends TestCase
{
    #[TestDox("Shall produce a key value mapping representing a path variable bindings")]
    #[TestWith(["/some/path/{id}", "/some/path/123", ["id" => "123"]])]
    #[TestWith(["/some/{name}/path/{id}", "/some/John/path/123", ["name" => "John", "id" => "123"]])]
    public function testa(string $pathWithVariables, string $rawPath, array $variableMapping)
    {
        $sut = new PathVariableBindings($pathWithVariables, $rawPath);

        $result = $sut->toArray();

        $this->assertSame($variableMapping, $result);
    }

    #[TestDox("Shall produce an empty array when there are no path variables")]
    #[TestWith(["/some/path/end", "/some/path/end", []])]
    public function testb(string $pathWithVariables, string $rawPath, array $variableMapping)
    {
        $sut = new PathVariableBindings($pathWithVariables, $rawPath);

        $result = $sut->toArray();

        $this->assertSame($variableMapping, $result);
    }
}
