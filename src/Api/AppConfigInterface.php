<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;

/**
 * Provides support for configuring the app.
 */
interface AppConfigInterface
{
    /**
     * Retrieves the attribute configuration.
     *
     * @api
     */
    public function getAll(): Collection;
}
