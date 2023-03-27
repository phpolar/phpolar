<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use TypeError;

/**
 * Provides support for configuring the properties
 * of an object for validation, formatting, and storage.
 */
abstract class AbstractModel
{
    use ColumnNameTrait;
    use DataTypeDetectionTrait;
    use EntityNameConfigurationTrait;
    use FieldErrorMessageTrait;
    use FormInputTypeDetectionTrait;
    use FormControlTypeDetectionTrait;
    use LabelFormatTrait;
    use PrimaryKeyTrait;
    use SizeConfigurationTrait;
    use ValidationTrait;

    /**
     * @param array<string|int,mixed>|object $data
     */
    public function __construct(array | object $data = [])
    {
        if (empty($data) === false) {
            $publicProps = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProps as $prop) {
                $propName = $prop->getName();
                $data = is_object($data) === false ? $data : get_object_vars($data);
                if (isset($data[$propName]) === true) {
                    $val = $data[$propName];
                    if (gettype($val) === "string") {
                        $type = $prop->getType();
                        // @codeCoverageIgnoreStart
                        if ($type instanceof ReflectionIntersectionType) {
                            // Parser will catch
                        }
                        // @codeCoverageIgnoreEnd
                        $casted = match (true) {
                            $type instanceof ReflectionNamedType => match ($type->getName()) {
                                "int" => (int) $val,
                                "float" => (float) $val,
                                "bool" => (bool) $val,
                                "string" => $val,
                                default => throw new TypeError(
                                    "Cannot automatically set string source values to non-scalar
                                     target properties.  Set the property manually."
                                ),
                            },
                            default => $val,
                        };
                        $prop->setValue($this, $casted);
                    }
                    $prop->setValue($this, $val);
                }
            }
        }
    }
}
