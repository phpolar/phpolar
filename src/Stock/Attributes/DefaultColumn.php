<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;

use Efortmeyer\Polar\Core\Defaults;

/**
 * Configures the default column format.
 */
final class DefaultColumn implements AttributeInterface
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __invoke(): string
    {
        $columnFormatter = Defaults::COLUMN_FORMATTER;
        return $columnFormatter($this->text);
    }
}
