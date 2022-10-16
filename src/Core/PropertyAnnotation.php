<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use Efortmeyer\Polar\Api\Attributes\Config\Collection as AttributeConfigCollection;
use Efortmeyer\Polar\Core\Attributes\Config\{
    AttributeConfig,
    AttributeConfigInterface,
    ConstructorArgs,
    ConstructorArgsNone,
    ConstructorArgsOne as ConfigConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropValWithSndArg,
};
use Efortmeyer\Polar\Core\Parsers\Annotation\{
    ConstructorArgsOne,
    ConstructorArgsNone as AnnotationConstructorArgsNone,
    ConstructorArgsOneWithValue,
    TypeTag,
};

use Efortmeyer\Polar\Core\Attributes\{
    AttributeCollection,
    Attribute,
};
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Provides a way to parse a class's property attributes.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class PropertyAnnotation
{
    private readonly string $docComment;

    private readonly string $propertyName;

    /**
     * @var mixed
     */
    private readonly mixed $propertyValue;

    public function __construct(
        object $instance,
        string $propertyName,
        private AttributeConfigCollection $attributeConfigMap
    ) {
        $reflectionProperty = new ReflectionProperty($instance, $propertyName);
        $docComment = $reflectionProperty->getDocComment();
        $this->docComment = $docComment === false ? "" : $docComment;
        $this->propertyName = $reflectionProperty->getName();
        $this->propertyValue = $reflectionProperty->isInitialized($instance) === true ? $reflectionProperty->getValue($instance) : $reflectionProperty->getDefaultValue();
    }

    /**
     * Uses this property's annotations to create a collection of attributes.
     */
    public function parse(): AttributeCollection
    {
        return new AttributeCollection(
            $this->attributeConfigMap
                ->filter(
                    fn (AttributeConfigInterface $config) =>
                        $config->isConfiguredForClass() === true ? $this->propertyValue === null || is_a($this->propertyValue, $config->forType()) : true
                )
                ->map($this->mapToAttribute(...))
                ->toArray()
        );
    }

    private function mapToAttribute(string $attributeConfigKey, AttributeConfig $attributeConfig): Attribute
    {
        $reflectionClass = new ReflectionClass($attributeConfigKey);
        $unqualifiedClassName = $reflectionClass->getShortName();
        $constructorArgType = $attributeConfig->getConstructorArgType();
        $defaultAttribute = $attributeConfig->getClassNameForDefaultAttribute();
        $defaultType = $attributeConfig->getConstructorArgTypeForDefault();
        $propertyValueArg = $constructorArgType instanceof ConstructorArgsPropValWithSndArg ? $this->propertyValue : null;
        $argsForDefault = $this->getArgsForDefault($defaultAttribute, $defaultType);
        $parserClassName = $this->getParserClassName($attributeConfigKey, $constructorArgType);

        return (new $parserClassName(
            $attributeConfigKey,
            $unqualifiedClassName,
            $defaultAttribute,
            $argsForDefault,
            $propertyValueArg)
        )->toToken($this->docComment)
            ->newInstance();
    }

    /**
     * @return string[]
     */
    private function getArgsForDefault(string $defaultAttribute, ConstructorArgs $defaultType): array
    {
        return match (true) {
            $defaultType instanceof ConstructorArgsPropertyName => [$this->propertyName],
            $defaultType instanceof ConstructorArgsPropertyValue => [$this->propertyValue],
            $defaultType instanceof ConstructorArgsNone => [],
            default => throw new InvalidArgumentException("Invalid Attribute config for ${defaultAttribute}")
        };
    }

    private function getParserClassName(string $attributeConfigKey, ConstructorArgs $constructorArgType): string
    {
        return match (true) {
            $constructorArgType instanceof ConstructorArgsPropertyName,
            $constructorArgType instanceof ConfigConstructorArgsOne => ConstructorArgsOne::class,
            $constructorArgType instanceof ConstructorArgsNone => AnnotationConstructorArgsNone::class,
            $constructorArgType instanceof ConstructorArgsPropValWithSndArg =>
                $attributeConfigKey === TypeValidation::class ? TypeTag::class : ConstructorArgsOneWithValue::class,
            default => throw new InvalidArgumentException("Invalid Attribute config for {$attributeConfigKey}")
        };
    }
}
