<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

use RuntimeException;

final class AttributeNotConfiguredException extends RuntimeException
{
    public $message = "Form control either does not exist or the default was not configured.";
}
