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

    /**
     * @var mixed
     */
    public $value;

    protected function __construct()
    {
    }

    public static function getFactory(AttributeCollection $attributes): FieldMetadataFactory
    {
        $className = $attributes->getFieldClassName();
        return FieldMetadataFactory::getInstance(new $className(), $attributes);
    }

    public function getValue()
    {
        return $this->value instanceof DateTimeInterface ? $this->value->format($this->dateFormat) : $this->value;
    }
}
