<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;;

use Attribute;

/**
 * Provides support for configuring the size of a column.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Size
{
    public function __construct(private int $size)
    {
    }

    /**
     * Returns the size (max length) of a column.
     *
     * @api
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
