<?php

declare(strict_types=1);


namespace Efortmeyer\Polar\Core\Parsers\Annotation;

use Efortmeyer\Polar\Stock\Attributes\NoopValidate;
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 */
class TypeTagTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseStringIntoExpectedAttribute()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * @var string
         */
        JAVASCRIPT;
        $expectedClass = TypeValidation::class;
        $sut = new TypeTag(TypeValidation::class, "", NoopValidate::class, ["FAKE_STRING"], "FAKE_STRING");
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }

    /**
     * @test
     */
    public function shouldParseStringIntoNoopValidateAttributeWhenKeywordIsAClassName()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * @var DateTimeImmutable
         */
        JAVASCRIPT;
        $expectedClass = NoopValidate::class;
        $sut = new TypeTag(TypeValidation::class, "", NoopValidate::class, [null], null);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultAttributeWhenAnnotationIsNotConfigured()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * DOES NOT HAVE ATTRIBUTE
         */
        JAVASCRIPT;
        $expectedClass = NoopValidate::class;
        $sut = new TypeTag(TypeValidation::class, "", NoopValidate::class, ["FAKE_STRING"]);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }
}
