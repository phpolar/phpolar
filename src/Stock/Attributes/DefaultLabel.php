<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Core\Defaults;

/**
 * Formats a label's text using the default format.
 */
final class DefaultLabel implements AttributeInterface
{
    /**
     * @var string
     */
    private $labelText;

    public function __construct(string $labelText)
    {
        $this->labelText = $labelText;
    }

    public function __invoke(): string
    {
        $fun = Defaults::LABEL_FORMATTER;
        return $fun($this->labelText);
    }
}
