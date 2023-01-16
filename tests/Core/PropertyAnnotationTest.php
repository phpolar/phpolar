<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core;

use Phpolar\Phpolar\Core\Attributes\Config\AttributeConfigInterface;
use Phpolar\Phpolar\Api\Attributes\Config\Collection;
use Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgs;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgsNone;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgsPropertyValue;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgsPropValWithSndArg;
use Phpolar\Phpolar\Stock\Attributes\Config\LabelKey;
use Phpolar\Phpolar\Stock\Attributes\Config\MaxLengthKey;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength;
use Phpolar\Phpolar\Stock\Attributes\Label;
use Phpolar\Phpolar\Stock\Attributes\MaxLength;
use Phpolar\Phpolar\Stock\Attributes\NoopValidate;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\PropertyAnnotation
 * @covers \Phpolar\Phpolar\Api\Attributes\Config\Collection
 * @covers \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @covers \Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig
 *
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Phpolar\Phpolar\Stock\Attributes\Config\LabelKey
 * @uses \Phpolar\Phpolar\Stock\Attributes\Config\MaxLengthKey
 * @uses \Phpolar\Phpolar\Stock\Attributes\Label
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\MaxLength
 */
class PropertyAnnotationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnDefaultAttributeWhenAttributeIsNotPresentInAnnotation()
    {
        $instance = new class()
        {
            /**
             * @var string
             * Doc block without label attribute
             */
            public $property;
        };
        $propertyName = "property";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($instance, $propertyName, $config);
        $this->assertTrue(
            $sut->parse()->containsClass(DefaultLabel::class)
        );
    }

    /**
     * @test
     */
    public function shouldReturnDefaultAttributeWhenPropertyDoesNotHaveAnnotation()
    {
        $instance = new class()
        {
            /**
             * @var mixed
             */
            public $property;
        };
        $propertyName = "property";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($instance, $propertyName, $config);
        $this->assertTrue(
            $sut->parse()->containsClass(DefaultLabel::class)
        );
    }

    /**
     * @test
     */
    public function shouldReturnAttributeWhenAttributeIsPresentInAnnotationWithNoArgs()
    {
        $instance = new class()
        {
            /**
             * @var mixed
             * @NoopValidate
             */
            public $property;
        };
        $propertyName = "property";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsNone());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsNone());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(NoopValidate::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($instance, $propertyName, $config);
        $this->assertTrue(
            $sut->parse()->containsClass(NoopValidate::class)
        );
    }

    /**
     * @test
     */
    public function shouldReturnLabelAttributeWhenLabelAttributeIsPresentInAnnotationWithPropertyNameArgs()
    {
        $instance = new class()
        {
            /**
             * @var mixed
             * @Label(My Property for $100.00)
             */
            public $property;
        };
        $propertyName = "property";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $attributesConfigFile = getcwd() . ATTRIBUTES_CONFIG_PATH;
        $config = include $attributesConfigFile;
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($instance, $propertyName, $config);
        $this->assertTrue(
            $sut->parse()->containsClass(Label::class)
        );
    }

    /**
     * @test
     */
    public function shouldReturnAttributeWhenAttributeIsPresentInAnnotationWithValueArg()
    {
        $instance = new class()
        {
            /**
             * @var mixed
             * @MaxLength(100)
             */
            public $property = "FAKE_STRING";
        };
        $propertyName = "property";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropValWithSndArg());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyValue());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultMaxLength::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new MaxLengthKey(), $attributeConfig);
        $sut = new PropertyAnnotation($instance, $propertyName, $config);
        $this->assertTrue(
            $sut->parse()->containsClass(MaxLength::class)
        );
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenConstructorArgTypeForDefaultNotConfigured()
    {
        $classNotConfigured = new class()
        {
            /**
             * @var string
             */
            public $fakeProperty = "";
        };
        $propertyName = "fakeProperty";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new class() extends ConstructorArgs
        {
        });
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($classNotConfigured, $propertyName, $config);
        $this->expectException(InvalidArgumentException::class);
        $sut->parse();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenConstructorArgTypeNotConfigured()
    {
        $classNotConfigured = new class()
        {
            /**
             * @var string
             */
            public $fakeProperty = "";
        };
        $propertyName = "fakeProperty";
        /**
         * @var Stub $configStub
         */
        $configStub = $this->createStub(AttributeConfig::class);
        $configStub->method("getConstructorArgType")->willReturn(new class() extends ConstructorArgs
        {
        });
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig
        {
            /**
             * @var AttributeConfigInterface
             */
            protected $configStub;

            public function __construct(AttributeConfigInterface $configStub)
            {
                $this->configStub = $configStub;
            }

            public function getConstructorArgType(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgType();
            }

            public function getConstructorArgTypeForDefault(): ConstructorArgs
            {
                return $this->configStub->getConstructorArgTypeForDefault();
            }

            public function getClassNameForDefaultAttribute(): string
            {
                return $this->configStub->getClassNameForDefaultAttribute();
            }

            public function forType(): string
            {
                return "";
            }

            public function isConfiguredForClass(): bool
            {
                return false;
            }
        };
        $config = new Collection();
        $config->add(new LabelKey(), $attributeConfig);
        $sut = new PropertyAnnotation($classNotConfigured, $propertyName, $config);
        $this->expectException(InvalidArgumentException::class);
        $sut->parse();
    }
}
