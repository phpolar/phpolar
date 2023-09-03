<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Extensions;

use Efortmeyer\Polar\Api\Comparable;
use PHPUnit\Framework\TestCase;

class PolarTestCaseExtension extends TestCase
{
    /**
     * Asserts that strings in an array contain a corresponding strings
     *
     * @param string[] $needles
     */
    public function assertStringArrayContainStrings(array $needles, string $haystack, string $message = ""): void
    {
        array_walk(
            $needles,
            function ($needle,  $a, $haystack) use ($message) {
                $this->assertStringContainsString($needle, $haystack, $message);
            },
            $haystack
        );
    }

    /**
     * Asserts that strings in an array contain a corresponding strings
     *
     * @param object[] $needles
     */
    public function assertContainsEqualsObject(object $needle, array $haystack, string $message = ""): void
    {
        array_walk(
            $haystack,
            function ($object,  $a, $needle) use ($message) {
                $this->assertObjectEquals($needle, $object, "equals", $message);
            },
            $needle
        );
    }

    /**
     * Asserts deep equality of two objects.
     */
    public function assertObjectDeepEquals(Comparable $it, Comparable $other): void
    {
        foreach ($it as $propertyName => $propertyValue) {
            if ($propertyValue instanceof Comparable) {
                if ($propertyValue->equals($other->$propertyName) === false) {
                    static::fail();
                }
            } else {
                if ($propertyValue !== $other->$propertyName) {
                    static::fail();
                }
            }
        }
        $this->expectNotToPerformAssertions();
    }
}
