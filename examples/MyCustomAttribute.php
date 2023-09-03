<?php

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;

/**
 * Converts the property name of a model
 * to lower case for it's column header.
 */
final class MyCustomAttribute implements AttributeInterface
{
    /**
     * @var string
     */
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __invoke()
    {
        return strtolower($this->text);
    }
}
