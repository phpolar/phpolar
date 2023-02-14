<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\Attributes\Config\Collection;

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
