<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Attributes;

/**
 * Provides a way to configure objects using Attributes.
 *
 * @example Person.php
 */
interface AttributeInterface
{
    /**
     * Makes the Attribute callable.
     *
     * @return mixed|void
     *
     * @api
     */
    public function __invoke();
}
