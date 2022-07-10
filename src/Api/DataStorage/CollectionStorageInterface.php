<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\DataStorage;

use Efortmeyer\Polar\Api\Model;

/**
 * Provides a way to interact with a list of items.
 */
interface CollectionStorageInterface
{
    /**
     * Saves an item to a collection.
     *
     * @api
     */
    public function save(Model $record): void;

    /**
     * Returns a list of items.
     *
     * @return Model[]
     *
     * @api
     */
    public function list(string $modelClassName);
}
