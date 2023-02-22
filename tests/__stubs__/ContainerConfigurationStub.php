<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use ArrayAccess;

final class ContainerConfigurationStub implements ArrayAccess
{
    private array $values = [];
    private array $raw = [];
    private array $keys = [];
    private array $frozen = [];
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->keys[$offset]);
    }
    public function offsetGet(mixed $offset): mixed
    {
        if (
            isset($this->raw[$offset]) === true
            || \is_object($this->values[$offset]) === false
            || \method_exists($this->values[$offset], '__invoke') === false
        ) {
            return $this->values[$offset];
        }

        $raw = $this->values[$offset];
        $val = $this->values[$offset] = $raw($this);
        $this->raw[$offset] = $raw;

        $this->frozen[$offset] = true;

        return $val;
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
        $this->keys[$offset] = true;
    }
    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset], $this->raw[$offset], $this->frozen[$offset]);
    }
}
