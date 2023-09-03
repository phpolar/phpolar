<?php

use Phpolar\Phpolar\Api\Attributes\Config\Key;

class MyCustomAttributeConfigKey extends Key
{
    public function getKey(): string
    {
        return static::$key;
    }

    public function __toString()
    {
        return static::$key;
    }
}
