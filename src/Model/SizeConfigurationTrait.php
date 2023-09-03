<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\SizeNotConfigured;
use Phpolar\Phpolar\Model\Size;
use Phpolar\Phpolar\Validation\MaxLength;
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
        $maxLenAttrs = $property->getAttributes(MaxLength::class);
        return match (count($sizeAttrs)) {
            0 => match (count($maxLenAttrs)) {
                0 => new SizeNotConfigured(),
                default => $maxLenAttrs[0]->getArguments()[0],
            },
            default => $sizeAttrs[0]->newInstance()->getSize(),
        };
    }
}
