<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Defaults;
use Efortmeyer\Polar\Stock\Field;
use PHPUnit\Framework\TestCase;

class FormControlData extends TestCase
{
    public static function fieldWithoutErrorsTestCases()
    {
        return [
            [Field::create("", [])],
        ];
    }

    public static function fieldErrorsTestCases()
    {
        $attributeStub = self::createStub(MaxLength::class);
        $attributeStub->method("isValid")
            ->willReturn(false);
        $attributeStub->method("getErrorMessage")
            ->willReturn(Defaults::ERROR_MESSAGE);
        $field = Field::create("", []);
        $field->validators[] = $attributeStub;

        return [
            [$field],
        ];
    }
}