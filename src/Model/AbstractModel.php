<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ReflectionObject;
use ReflectionProperty;

;

/**
 * Provides support for configuring the properties
 * of an object for validation, formatting, and storage.
 */
abstract class AbstractModel
{
    use ValidationTrait;
    use FieldErrorMessageTrait;
    use LabelFormatTrait;
    use ColumnNameTrait;
    use DataTypeDetectionTrait;
    use EntityNameConfigurationTrait;
    use SizeConfigurationTrait;
    use FormInputTypeDetectionTrait;
    use FormControlTypeDetectionTrait;

    /**
     * @param array<string|int,mixed>|object $data
     */
    public function __construct(array | object $data = [])
    {
        if (empty($data) === false) {
            $publicProps = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProps as $prop) {
                $propName = $prop->getName();
                $this->$propName = (is_object($data) === true ? ($data->$propName ?? $this->$propName) : ($data[$propName] ?? $this->$propName));
            }
        }
    }
}
