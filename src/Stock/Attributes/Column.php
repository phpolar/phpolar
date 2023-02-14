<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Phpolar\Phpolar\Core\Attributes\Attribute;

/**
 * Configures a property's column name.
 */
#[GlobalAttribute(GlobalAttribute::TARGET_PROPERTY)]
final class Column extends Attribute
{
    public function __construct(private readonly string $text)
    {
    }

    public function __invoke(): string
    {
        return $this->text;
    }

    public function isColumn(): bool
    {
        return true;
    }
}
