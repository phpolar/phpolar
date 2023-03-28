<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use IteratorAggregate;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use Traversable;
use TypeError;

/**
 * Provides support for configuring the properties
 * of an object for validation, formatting, and storage.
 *
 * @template-implements IteratorAggregate<string,mixed>
 */
abstract class AbstractModel implements IteratorAggregate
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
                                DateTime::class => new DateTime($val),
                                DateTimeInterface::class,
                                DateTimeImmutable::class => new DateTimeImmutable($val),
                                default => throw new TypeError(
                                    "Cannot automatically set string source values to non-scalar
                                     target properties.  Set the property manually."
                                ),
                            },
                            default => $val,
                        };
                        $prop->setValue($this, $casted);
                        continue;
                    }
                    $prop->setValue($this, $val);
                }
            }
        }
    }

    /**
     * @return Traversable<string,mixed>
     */
    public function getIterator(): Traversable
    {
        /**
         * We only want public properties
         * so we are using this other
         * object. Yeah, we could have
         * used Reflection. But there's
         * more than one way to get
         * public properties off an
         * object.
         */
        $anotherWay = new class () {
            /**
             * Get the public properties.
             *
             * @return array<string,mixed>
             */
            public function getPubProps(object $model): array
            {
                return array_merge(
                    get_class_vars($model::class),
                    get_object_vars($model)
                );
            }
        };
        /**
         * Iteration over models needs
         * to support iteration of non-initialized
         * public properties.
         */
        $allPublicProps = $anotherWay->getPubProps($this);
        foreach ($allPublicProps as $propName => $propVal) {
            yield $propName => $propVal;
        }
    }
}
