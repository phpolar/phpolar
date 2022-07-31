<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

use RuntimeException;

final class RequiredAttributeNotFoundException extends RuntimeException
{
    public $message = "Required attribute not found";
}