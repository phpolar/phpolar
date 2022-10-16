<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Core\{
    Entry,
    Comparable,
};

/**
 * Represents a unit of data.
 *
 * @example Person.php
 */
abstract class Model extends Entry implements Comparable
{
    /**
     * Determines if the object is equal to another.
     *
     * @api
     */
    public function equals(Comparable $other): bool
    {
        $properties = get_object_vars($this);
        $propsOfOtherObj = get_object_vars($other);
        $matches = array_filter($properties, fn ($value) => in_array($value, $propsOfOtherObj));
        return count($matches) === count($properties);
    }
}
