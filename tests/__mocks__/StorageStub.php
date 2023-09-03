<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Mocks;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageInterface;
use Phpolar\Phpolar\Api\Model;

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
