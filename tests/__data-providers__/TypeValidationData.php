<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Stock\Validation\ScalarTypes;
use Efortmeyer\Polar\Tests\Mocks\SerializableMock;
use Serializable;
use stdClass;

class TypeValidationData
{
    private const BOOLEAN_VALUES = [true, false];

    private const FLOAT_VALUES = [1.2, 1.3, 1.4, 1.5, 1.6];

    public static function valid()
    {
        return [
            [str_repeat("a", random_int(1, 20)),                       ScalarTypes::STRING],
            [random_int(1, 2000),                                      ScalarTypes::INTEGER],
            [self::FLOAT_VALUES[array_rand(self::FLOAT_VALUES)],       ScalarTypes::FLOAT],
            [self::FLOAT_VALUES[array_rand(self::FLOAT_VALUES)],       ScalarTypes::DOUBLE],
            [self::BOOLEAN_VALUES[array_rand(self::BOOLEAN_VALUES)],   ScalarTypes::BOOL],
            [null,                                                     ScalarTypes::NULL],
            [new SerializableMock(),                                   Serializable::class],
        ];
    }

    public static function invalid()
    {
        return [
            [random_int(1, 2000),                ScalarTypes::STRING],
            [str_repeat("a", random_int(1, 20)), ScalarTypes::INTEGER],
            [str_repeat("a", random_int(1, 20)), ScalarTypes::BOOL],
            [new stdClass(),                     Serializable::class],
            [new stdClass(),                     stdClass::class],
        ];
    }
}
