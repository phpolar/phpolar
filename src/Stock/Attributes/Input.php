<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;

/**
 * Configures a property to a given form control type.
 */
final class Input implements AttributeInterface
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __invoke(): string
    {
        return $this->type;
    }
}
