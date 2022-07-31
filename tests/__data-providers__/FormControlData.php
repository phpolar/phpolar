<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Api\UIElements\Messages;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

class FormControlData extends TestCase
{
    public static function fieldWithoutErrorsTestCases()
    {
        return [
            [FieldMetadata::getFactory(new AttributeCollection([RequiredAttributes::get()]))->create("", "")],
        ];
    }

    public static function fieldErrorsTestCases()
    {
        $attributeStub = self::createStub(MaxLength::class);
        $attributeStub->method("isValid")
            ->willReturn(false);
        $attributeStub->method("getErrorMessage")
            ->willReturn(Messages::ERROR_MESSAGE);
        $field = FieldMetadata::getFactory(new AttributeCollection([RequiredAttributes::get()]))->create("", "");
        $field->validators[] = $attributeStub;

        return [
            [$field],
        ];
    }
}