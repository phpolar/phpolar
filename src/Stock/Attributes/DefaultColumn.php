<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;

/**
 * Configures the default column format.
 */
final class DefaultColumn extends Attribute
{
    public function __construct(private readonly string $text)
    {
    }

    public function __invoke(): string
    {
        $columnFormatter = Defaults::COLUMN_FORMATTER;
        return $columnFormatter($this->text);
    }

    public function isColumn(): bool
    {
        return true;
    }
}
