<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;;

use Attribute;

/**
 * Allows for optional configuring of the name of an entity.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class EntityName
{
    public function __construct(private string $name)
    {
    }

    /**
     * Returns the name of the entity.
     * @api
     */
    public function getName(): string
    {
        return $this->name;
    }
}