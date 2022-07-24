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
     * @return mixed
     * @api
     */
    private static function encode($it)
    {
        switch (true) {
            case $it instanceof Stringable:
                return HtmlEncoder::encodeString((string) $it);
            case $it instanceof Serializable:
                return HtmlEncoder::encodeString($it->serialize() ?? "");
            case $it instanceof Closure:
                return HtmlEncoder::EMPTY_STRING;
            case is_string($it):
                return HtmlEncoder::encodeString($it);
            case is_bool($it):
            case is_float($it):
            case is_integer($it):
                return HtmlEncoder::skip($it);
            case is_iterable($it):
                return HtmlEncoder::encodeArray((array) $it);
            case is_object($it):
                return HtmlEncoder::encodeProperties($it); // @codeCoverageIgnore
            default:
                return HtmlEncoder::EMPTY_STRING;
        }
    }

    /**
     * @param bool|int|float $it
     * @return bool|int|float
     */
    private static function skip($it)
    {
        return $it;
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
