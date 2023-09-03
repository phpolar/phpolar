<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Api\UIElements\Messages;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Fields\FieldMetadataConfig;
use Efortmeyer\Polar\Core\Fields\FieldMetadataFactory;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

class FormControlData extends TestCase
{
    private static function getFactory(AttributeCollection $attrs): FieldMetadataFactory
    {
        $className = $attrs->getFieldClassName();
        return new FieldMetadataFactory(
            new $className(),
            new FieldMetadataConfig($attrs),
        );
    }

    public static function fieldWithoutErrorsTestCases()
    {
        return [
            [self::getFactory(new AttributeCollection([RequiredAttributes::get()]))->create("", "")],
        ];
    }

    public static function fieldErrorsTestCases()
    {
        $attributeStub = self::createStub(MaxLength::class);
        $attributeStub->method("isValid")
            ->willReturn(false);
        $attributeStub->method("getErrorMessage")
            ->willReturn(Messages::ERROR_MESSAGE);
        $field = self::getFactory(new AttributeCollection([RequiredAttributes::get()]))->create("", "");
        $field->validators[] = $attributeStub;

        return [
            [$field],
        ];
    }
}
