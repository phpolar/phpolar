<?php

declare(strict_types=1);

namespace Phpolar\Validation\Exception;

use Phpolar\Phpolar\Tests\Stubs\MessagesStub;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionProperty;

use function Phpolar\Phpolar\Validation\Functions\getMessageGetters;

#[CoversFunction("\\Phpolar\\Phpolar\\Validation\\Functions\\getMessageGetters")]
final class GetMessageGettersTest extends TestCase
{
    #[TestDox("Shall retrieve validator attributes.")]
    public function test1()
    {
        $obj = new class () {
            #[MessagesStub]
            public string $property = "stub prop";
        };
        $props = (new ReflectionObject($obj))->getProperties(ReflectionProperty::IS_PUBLIC);
        $this->assertNotEmpty($props, "Test object did not contain properties");
        foreach ($props as $prop) {
            $suts = getMessageGetters($prop, $obj);
            $this->assertNotEmpty($suts, "Messages attributes were not retrieved");
            $this->assertContainsOnlyInstancesOf(MessagesStub::class, $suts);
        }
    }

    #[TestDox("Shall return an empty array when no validator attributes are present.")]
    public function test2()
    {
        $obj = new class () {
            public string $property = "stub prop";
        };
        $props = (new ReflectionObject($obj))->getProperties(ReflectionProperty::IS_PUBLIC);
        $this->assertNotEmpty($props, "Test object did not contain properties");
        foreach ($props as $prop) {
            $suts = getMessageGetters($prop, $obj);
            $this->assertEmpty($suts, "Messages attributes were retrieved");
        }
    }
}
