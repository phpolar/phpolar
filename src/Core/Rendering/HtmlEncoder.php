<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Rendering;

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
    private static function encode($it): mixed
    {
        if (HtmlEncoder::canEncode($it) === true) {
            return HtmlEncoder::encodeString($it);
        } else if (HtmlEncoder::shouldSkip($it) === true) {
            return HtmlEncoder::skip($it);
        } else if (HtmlEncoder::isSerializable($it) === true) {
            return HtmlEncoder::serializeValue($it);
        } else if (is_iterable($it) === true) {
            return HtmlEncoder::encodeArray((array) $it);
        } else if (is_object($it) === true && $it instanceof Closure === false) {
            return HtmlEncoder::encodeProperties($it); // @codeCoverageIgnore
        } else {
            return HtmlEncoder::EMPTY_STRING;
        }
    }

    private static function isSerializable($it): bool
    {
        return $it instanceof Stringable || $it instanceof Serializable;
    }

    private static function shouldSkip($it): bool
    {
        return is_bool($it) === true || is_float($it) === true || is_integer($it) === true;
    }

    private static function canEncode($it): bool
    {
        return is_string($it) === true;
    }

    private static function skip(bool|int|float $it): bool|int|float
    {
        return $it;
    }

    private static function serializeValue(Serializable|Stringable $it): string
    {
        return HtmlEncoder::encodeString($it instanceof Serializable ? (string) $it->serialize() : (string) $it);
    }

    private static function encodeString(string $str): string
    {
        return htmlentities($str, ENT_QUOTES | ENT_HTML5);
    }

    private static function encodeArray(array $arr): array
    {
        return array_map([HtmlEncoder::class, "encode"], $arr);
    }

    /**
     * Recursively and immutably encodes the object's members.
     *
     * @api
     */
    public static function encodeProperties(object $obj): object
    {
        $copy = clone $obj;
        foreach ($copy as $propertyName => $propertyValue) {
            $copy->$propertyName = HtmlEncoder::encode($propertyValue);
        }
        return $copy;
    }
}
