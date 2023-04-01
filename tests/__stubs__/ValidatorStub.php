<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Attribute;
use Phpolar\Validator\ValidatorInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ValidatorStub implements ValidatorInterface
{
    public function isValid(): bool
    {
        return true;
    }
}
