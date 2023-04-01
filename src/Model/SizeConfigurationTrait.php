<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\SizeNotConfigured;
use Phpolar\Phpolar\Model\Size;
use ReflectionProperty;

/**
 * Allows for configuring the size of an entity
 */
trait SizeConfigurationTrait
{
    /**
     * Returns the size if configured.
     *
     * @api
     */
    public function getSize(string $propName): int|SizeNotConfigured
    {
        $property = new ReflectionProperty($this, $propName);
        $sizeAttrs = $property->getAttributes(Size::class);
        return match (count($sizeAttrs)) {
            0 => new SizeNotConfigured(),
            default => $sizeAttrs[0]->newInstance()->getSize(),
        };
    }
}
