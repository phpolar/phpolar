<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use Efortmeyer\Polar\Api\Attributes\Config\AttributeConfigInterface;
use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgs;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsNone;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyValue;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyValueWithSecondArg;
use Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig;
use Efortmeyer\Polar\Stock\Attributes\Config\LabelKey;
use Efortmeyer\Polar\Stock\Attributes\Config\MaxLengthKey;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength;
use Efortmeyer\Polar\Stock\Attributes\Label;
use Efortmeyer\Polar\Stock\Attributes\MaxLength;
use Efortmeyer\Polar\Stock\Attributes\NoopValidate;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\PropertyAnnotation
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\LabelKey
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\MaxLengthKey
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @testdox PropertyAnnotation
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
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $this->assertContainsOnlyInstancesOf(
            DefaultLabel::class,
            array_filter($sut->parse(), fn ($it) => $it instanceof DefaultLabel)
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
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $this->assertContainsOnlyInstancesOf(
            DefaultLabel::class,
            array_filter($sut->parse(), fn ($it) => $it instanceof DefaultLabel)
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
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $this->assertContainsOnlyInstancesOf(NoopValidate::class, $sut->parse());
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
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $this->assertContainsOnlyInstancesOf(Label::class, $sut->parse());
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
        $configStub->method("getConstructorArgType")->willReturn(new ConstructorArgsPropertyValueWithSecondArg());
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyValue());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultMaxLength::class);
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $this->assertContainsOnlyInstancesOf(MaxLength::class, $sut->parse());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenConstructorArgTypeForDefaultNotConfigured()
    {
        $classNotConfigured = new class() {
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
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new class() extends ConstructorArgs {});
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
        $classNotConfigured = new class() {
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
        $configStub->method("getConstructorArgType")->willReturn(new class() extends ConstructorArgs {});
        $configStub->method("getConstructorArgTypeForDefault")->willReturn(new ConstructorArgsPropertyName());
        $configStub->method("getClassNameForDefaultAttribute")->willReturn(DefaultLabel::class);
        $attributeConfig = new class($configStub) extends AttributeConfig {
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
