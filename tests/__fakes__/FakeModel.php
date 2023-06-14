<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Fakes;

use Phpolar\Model\AbstractModel;

final class FakeModel extends AbstractModel
{
    public function __construct(public string $title = "Add a fake model", public string $myInput = "what")
    {
    }

    public function equals(self $other): bool
    {
        return $this->title === $other->title &&
            $this->myInput === $other->myInput;
    }
}
