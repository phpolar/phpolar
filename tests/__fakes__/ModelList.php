<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Fakes;

use Phpolar\Phpolar\Model\AbstractModel;

final class ModelList extends AbstractModel
{
    public string $title = "FAKE LIST";

    public string $successMsg = "You did it!";

    /**
     * @var FakeModel[]
     */
    private array $items = [];

    public function add(FakeModel $model)
    {
        $this->items[] = $model;
    }

    /**
     * @return FakeModel[]
     */
    public function list(): array
    {
        return $this->items;
    }
}
