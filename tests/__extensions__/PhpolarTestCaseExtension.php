<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Extensions;

use Phpolar\Phpolar\Core\Comparable;
use PHPUnit\Framework\TestCase;

class PhpolarTestCaseExtension extends TestCase
{
    protected static function getTestFileName(string $extension): string
    {
        return implode(DIRECTORY_SEPARATOR, [$_SERVER["TMP"] ?? "/tmp", uniqid() . $extension]);
    }

    /**
     * Asserts that strings in an array contain a corresponding strings
     *
     * @param string[] $needles
     */
    public function assertStringArrayContainStrings(array $needles, string $haystack, string $message = ""): void
    {
        array_walk(
            $needles,
            fn ($needle,  $a, $haystack) => $this->assertStringContainsString($needle, $haystack, $message),
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
            fn ($object,  $a, $needle) => $this->assertObjectEquals($needle, $object, "equals", $message),
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
