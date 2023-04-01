<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Attribute;
use Phpolar\Validator\MessageGetterInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MessagesStub implements MessageGetterInterface
{
    public const TEST_MESSAGE = "TEST MESSAGE";
    public function getMessages(): array
    {
        return [self::TEST_MESSAGE];
    }
}
