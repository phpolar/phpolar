<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Attribute;
use Phpolar\Validator\MessageGetterInterface;
use Phpolar\Validator\ValidatorInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ValidPropertyStub implements ValidatorInterface, MessageGetterInterface
{
    public const EXPECTED_MESSAGE = "IS VALID";

    public function isValid(): bool
    {
        return true;
    }

    public function getMessages(): array
    {
        return [];
    }
}
