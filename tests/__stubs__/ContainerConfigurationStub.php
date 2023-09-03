<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use ArrayAccess;

final class ContainerConfigurationStub implements ArrayAccess
{
    private array $values = [];
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }
    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset];
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }
    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }
}
