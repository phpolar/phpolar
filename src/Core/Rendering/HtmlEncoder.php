<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Rendering;

use Closure;
use Serializable;
use Stringable;

/**
 * Provides a way to sanitize a template's variables.
 */
class HtmlEncoder
{
    private const EMPTY_STRING = "";

    /**
     * Recursively sanitizes a given value.
     *
     * When given an array or object, it's members
     * will be recursively sanitized.
     *
     * @api
     */
    private function encode(mixed $thing): mixed
    {
        if (static::canEncode($thing) === true) {
            return static::encodeString($thing);
        } else if (static::shouldSkip($thing) === true) {
            return static::skip($thing);
        } else if (static::isSerializable($thing) === true) {
            return static::serializeValue($thing);
        } else if (is_iterable($thing) === true) {
            return $this->encodeArray((array) $thing);
        } else if (is_object($thing) === true && $thing instanceof Closure === false) {
            return $this->encodeProperties($thing); // @codeCoverageIgnore
        }
        return static::EMPTY_STRING;
    }

    private static function isSerializable(mixed $thing): bool
    {
        return $thing instanceof Stringable || $thing instanceof Serializable;
    }

    private static function shouldSkip(mixed $thing): bool
    {
        return is_bool($thing) === true || is_float($thing) === true || is_integer($thing) === true;
    }

    private static function canEncode(mixed $thing): bool
    {
        return is_string($thing) === true;
    }

    private static function skip(bool|int|float $thing): bool|int|float
    {
        return $thing;
    }

    private static function serializeValue(Serializable|Stringable $thing): string
    {
        return static::encodeString($thing instanceof Serializable ? (string) $thing->serialize() : (string) $thing);
    }

    private static function encodeString(string $str): string
    {
        return htmlentities($str, ENT_QUOTES | ENT_HTML5);
    }

    private function encodeArray(array $arr): array
    {
        return array_map($this->encode(...), $arr);
    }

    /**
     * Recursively and immutably encodes the object's members.
     *
     * @api
     */
    public function encodeProperties(object $obj): object
    {
        $copy = clone $obj;
        foreach ($copy as $propertyName => $propertyValue) {
            $copy->$propertyName = $this->encode($propertyValue);
        }
        return $copy;
    }
}
