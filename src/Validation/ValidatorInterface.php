<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

/**
 * Unifies configuration objects with a standard api.
 *
 * @api
 */
interface ValidatorInterface
{
    /**
     * Use to determine if a configuration is valid.
     *
     * @api
     */
    public function isValid(): bool;
}
