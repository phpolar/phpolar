<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Attribute;
use Phpolar\Validator\MessageGetterInterface;
use Phpolar\Validator\ValidatorInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class InvalidPropertyStub implements ValidatorInterface, MessageGetterInterface
{
    public const EXPECTED_MESSAGE = "IS INVALID";

    public function isValid(): bool
    {
        return false;
    }

    public function getMessages(): array
    {
        return [self::EXPECTED_MESSAGE];
    }
}
