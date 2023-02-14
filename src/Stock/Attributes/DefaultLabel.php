<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;

/**
 * Formats a label's text using the default format.
 */
final class DefaultLabel extends Attribute
{
    public function __construct(private readonly string $labelText)
    {
    }

    public function __invoke(): string
    {
        $fun = Defaults::LABEL_FORMATTER;
        return $fun($this->labelText);
    }

    public function isLabel(): bool
    {
        return true;
    }
}
