<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Core\Defaults;

/**
 * Configures the default form control type.
 */
final class DefaultFormControl implements AttributeInterface
{
    public function __construct()
    {
    }

    public function __invoke(): string
    {
        return Defaults::FORM_CONTROL_TYPE;
    }
}
