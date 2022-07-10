<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Api\Attributes\Config\{
    AttributeConfigInterface,
    Collection as AttributeConfigCollection,
};
use Efortmeyer\Polar\Core\Attributes\Config\{
    ConstructorArgs,
    ConstructorArgsNone,
    ConstructorArgsOne as ConfigConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropertyValueWithSecondArg,
};
use Efortmeyer\Polar\Core\Parsers\Annotation\{
    ConstructorArgsOne,
    ConstructorArgsNone as AnnotationConstructorArgsNone,
    ConstructorArgsOneWithValue,
    TypeTag,
};
use Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig;

use Closure;
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Provides a way to parse a class's property attributes.
 */
final class PropertyAnnotation
{
    /**
     * @var string
     */
    private $docComment;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var mixed
     */
    private $propertyValue;

    /**
     * @var AttributeConfigCollection
     */
    private $attributeConfigMap;

    /**
     * @var Closure
     */
    private static $onlyRequired;

    /**
     * @var Closure
     */
    private static $toAttribute;

    public function __construct(
        object $instance,
        string $propertyName,
        AttributeConfigCollection $attributeConfigMap
    ) {
        $reflectionProperty = new ReflectionProperty($instance, $propertyName);
        $docComment = $reflectionProperty->getDocComment();
        $this->docComment = $docComment === false ? "" : $docComment;
        $this->propertyName = $reflectionProperty->getName();
        $this->propertyValue = $reflectionProperty->getValue($instance);
        $this->attributeConfigMap = $attributeConfigMap;
        static::$onlyRequired = Closure::fromCallable([$this, "filterRequiredAttributes"]);
        static::$toAttribute = Closure::fromCallable([$this, "mapToAttribute"]);
    }

    /**
     * Uses this property's annotations to create a list of attributes.
     *
     * @return AttributeInterface[]
     */
    public function parse(): array
    {
        return $this->attributeConfigMap
            ->filter(static::$onlyRequired)
            ->map(static::$toAttribute)
            ->toArray();
    }

    private function filterRequiredAttributes(AttributeConfigInterface $config): bool
    {
        return $config->isConfiguredForClass() === true ? $this->propertyValue === null || is_a($this->propertyValue, $config->forType()) : true;
    }

    private function mapToAttribute(string $attributeConfigKey, AttributeConfig $attributeConfig): AttributeInterface
    {
        $reflectionClass = new ReflectionClass($attributeConfigKey);
        $unqualifiedClassName = $reflectionClass->getShortName();
        $constructorArgType = $attributeConfig->getConstructorArgType();
        $defaultAttribute = $attributeConfig->getClassNameForDefaultAttribute();
        $defaultConstructorArgType = $attributeConfig->getConstructorArgTypeForDefault();
        $propertyValueConstructorArg = $constructorArgType instanceof ConstructorArgsPropertyValueWithSecondArg ? $this->propertyValue : null;
        $argsForDefault = $this->getArgsForDefault($defaultAttribute, $defaultConstructorArgType);
        $parserClassName = $this->getParserClassName($attributeConfigKey, $constructorArgType);

        return (new $parserClassName(
            $attributeConfigKey,
            $unqualifiedClassName,
            $defaultAttribute,
            $argsForDefault,
            $propertyValueConstructorArg)
        )->toToken($this->docComment)
            ->newInstance();
    }

    /**
     * @return string[]
     */
    private function getArgsForDefault(string $defaultAttribute, ConstructorArgs $defaultConstructorArgType): array
    {
        switch (true) {
            case $defaultConstructorArgType instanceof ConstructorArgsPropertyName:
                return [$this->propertyName];
            case $defaultConstructorArgType instanceof ConstructorArgsPropertyValue:
                return [$this->propertyValue];
            case $defaultConstructorArgType instanceof ConstructorArgsNone:
                return [];
            default:
                throw new InvalidArgumentException(
                    "Invalid Attribute config for ${defaultAttribute}"
                );
        }
    }

    private function getParserClassName(string $attributeConfigKey, ConstructorArgs $constructorArgType): string
    {
        switch (true) {
            case $constructorArgType instanceof ConstructorArgsPropertyName:
            case $constructorArgType instanceof ConfigConstructorArgsOne:
                return ConstructorArgsOne::class;
            case $constructorArgType instanceof ConstructorArgsPropertyValueWithSecondArg:
                return $attributeConfigKey === TypeValidation::class ? TypeTag::class : ConstructorArgsOneWithValue::class;
            case $constructorArgType instanceof ConstructorArgsNone:
                return AnnotationConstructorArgsNone::class;
            default:
                throw new InvalidArgumentException(
                    "Invalid Attribute config for {$attributeConfigKey}"
                );
        }
    }
}
