<?php

declare(strict_types=1);


namespace Phpolar\Phpolar\Core\Parsers\Annotation;

use Phpolar\Phpolar\Stock\Attributes\NoopValidate;
use Phpolar\Phpolar\Stock\Attributes\TypeValidation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Attributes\NoopValidate
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
