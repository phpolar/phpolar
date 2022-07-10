<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgs;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsNone;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyValue;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyValueWithSecondArg;


use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig
 * @testdox AttributeConfig
 */
class AttributeConfigTest extends TestCase
{
    /**
     * @return \Efortmeyer\Polar\Stock\Attributes\Config\ConstructorArgs[]
     */
    public static function constructorArgs()
    {
        return [
            [ConstructorArgsNone::class, new ConstructorArgsNone()],
            [ConstructorArgsPropertyName::class, new ConstructorArgsPropertyName()],
            [ConstructorArgsPropertyValue::class, new ConstructorArgsPropertyValue()],
            [ConstructorArgsPropertyValueWithSecondArg::class, new ConstructorArgsPropertyValueWithSecondArg()],
        ];
    }

    /**
     * @return string[]
     */
    public static function classNames()
    {
        return [
            [DefaultLabel::class],
            [DefaultColumn::class],
            [DefaultMaxLength::class],
        ];
    }


    /**
     * @test
     * @dataProvider constructorArgs
     * @testdox getConstructorArgType should return configured value
     */
    public function getConstructorArgType__shouldReturnConfiguredValue(string $expectedClassName, ConstructorArgs $givenArgsType)
    {
        $sut = new AttributeConfig($givenArgsType, DefaultLabel::class, $givenArgsType);
        $this->assertInstanceOf($expectedClassName, $sut->getConstructorArgType());
    }

    /**
     * @test
     * @dataProvider constructorArgs
     * @testdox getConstructorArgTypeForDefault should return configured value
     */
    public function getConstructorArgTypeForDefault__shouldReturnConfiguredValue(
        string $expectedClassName,
        ConstructorArgs $givenArgsType
    ) {
        $sut = new AttributeConfig($givenArgsType, DefaultLabel::class, $givenArgsType);
        $this->assertInstanceOf($expectedClassName, $sut->getConstructorArgTypeForDefault());
    }

    /**
     * @test
     * @dataProvider classNames
     * @testdox getClassNameForDefaultAttribute should return configured value
     */
    public function getClassNameForDefaultAttribute__shouldReturnConfiguredValue(string $givenClassName)
    {
        $sut = new AttributeConfig(new ConstructorArgsNone(), $givenClassName, new ConstructorArgsNone());
        $this->assertSame($givenClassName, $sut->getClassNameForDefaultAttribute());
    }

    /**
     * @test
     * @testdox isConfiguredForClass should determine if instance is configured for any class
     */
    public function isConfiguredForClass__shouldDetermineIfInstanceIsConfiguredForAnyClass()
    {
        $notConfigured = new AttributeConfig(new ConstructorArgsNone(), DefaultLabel::class, new ConstructorArgsNone());
        $configured = new AttributeConfig(new ConstructorArgsNone(), DefaultLabel::class, new ConstructorArgsNone(), Label::class);
        $this->assertFalse($notConfigured->isConfiguredForClass());
        $this->assertTrue($configured->isConfiguredForClass());
    }

    /**
     * @test
     * @dataProvider classNames
     * @testdox forType should return the class the instance is configured for
     */
    public function forType__shouldReturnTheClassTheInstanceIsConfiguredFor(string $givenClassName)
    {
        $sut = new AttributeConfig(new ConstructorArgsNone(), DefaultLabel::class, new ConstructorArgsNone(), $givenClassName);
        $this->assertSame($givenClassName, $sut->forType());
    }

    /**
     * @test
     * @testdox forType should return an empty string if the instance is not configured for a class
     */
    public function forType__shouldReturnAnEmptyStringIfTheInstanceIsNotConfiguredForAClass()
    {
        $sut = new AttributeConfig(new ConstructorArgsNone(), DefaultLabel::class, new ConstructorArgsNone());
        $this->assertEmpty($sut->forType());
    }
}
