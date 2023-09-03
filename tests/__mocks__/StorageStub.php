<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;
use Efortmeyer\Polar\Api\Model;

class StorageStub implements CollectionStorageInterface
{
    public function save(Model $model): void
    {
        // noop
    }


    public function list(string $modelClassName): array
    {
        return [];
    }
}
