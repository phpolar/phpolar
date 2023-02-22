<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Phpolar\Phpolar\Validation\ValidatorInterface;

final class ValidatorWithNoErrorMessage implements ValidatorInterface
{
    public function isValid(): bool
    {
        return false;
    }
}
