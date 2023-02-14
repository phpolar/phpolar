<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Attributes;

use RuntimeException;

final class RequiredAttributeNotFoundException extends RuntimeException
{
    public $message = "Required attribute not found";
}
