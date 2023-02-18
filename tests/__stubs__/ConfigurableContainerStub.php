<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use ArrayAccess;
use Closure;
use Psr\Container\ContainerInterface;

final class ConfigurableContainerStub implements ContainerInterface
{
    /**
     * @param ArrayAccess<string,mixed> $config
     */
    public function __construct(private ArrayAccess &$config)
    {
    }

    public function has(string $id): bool
    {
        return isset($this->config[$id]);
    }

    public function get(string $id)
    {
        $configured = $this->config[$id];
        return $configured instanceof Closure ? $configured($this) : $configured;
    }
}
