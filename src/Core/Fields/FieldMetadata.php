<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;

use DateTimeInterface;

/**
 * Provides metadata for a field.
 */
class FieldMetadata
{
    public string $label = "";

    public string $formControlType = "";

    public string $column = "";

    public string $propertyName = "";

    public string $dateFormat = "";

    /**
     * @var ValidationInterface[]
     */
    public array $validators = [];

    public mixed $value;

    public function __construct()
    {
    }

    public function getValue(): mixed
    {
        return $this->value instanceof DateTimeInterface ? $this->value->format($this->dateFormat) : $this->value;
    }
}
