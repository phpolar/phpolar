<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Stock\Validation\ScalarTypes;
use Phpolar\Phpolar\Tests\Mocks\SerializableMock;
use Serializable;
use stdClass;

class TypeValidationData
{
    private const BOOLEAN_VALUES = [true, false];

    private const FLOAT_VALUES = [1.2, 1.3, 1.4, 1.5, 1.6];

    public static function valid()
    {
        return [
            [str_repeat("a", random_int(1, 20)),                       ScalarTypes::String->value],
            [random_int(1, 2000),                                      ScalarTypes::Integer->value],
            [self::FLOAT_VALUES[array_rand(self::FLOAT_VALUES)],       ScalarTypes::Float->value],
            [self::FLOAT_VALUES[array_rand(self::FLOAT_VALUES)],       ScalarTypes::Double->value],
            [self::BOOLEAN_VALUES[array_rand(self::BOOLEAN_VALUES)],   ScalarTypes::Bool->value],
            [null,                                                     ScalarTypes::Null->value],
            [new SerializableMock(),                                   Serializable::class],
        ];
    }

    public static function invalid()
    {
        return [
            [random_int(1, 2000),                ScalarTypes::String->value],
            [str_repeat("a", random_int(1, 20)), ScalarTypes::Integer->value],
            [str_repeat("a", random_int(1, 20)), ScalarTypes::Bool->value],
            [new stdClass(),                     Serializable::class],
            [new stdClass(),                     stdClass::class],
        ];
    }
}
