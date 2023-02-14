<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;

/**
 * Configures the default form control type.
 */
final class DefaultFormControl extends Attribute
{
    public function __construct()
    {
    }

    public function __invoke(): string
    {
        return Defaults::FORM_CONTROL_TYPE;
    }

    public function isInput(): bool
    {
        return true;
    }

    public function isFormControl(): bool
    {
        return true;
    }

    public function getFieldClassName(): string
    {
        return Defaults::FORM_CONTROL_FIELD_CLASS_NAME;
    }
}
