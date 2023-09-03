<?php

declare(strict_types=1);

use Phpolar\Phpolar\Api\Model;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\Model
 * @covers \Phpolar\Phpolar\Api\Attributes\Config\Collection
 * @covers \Phpolar\Phpolar\Core\Entry
 * @covers \Phpolar\Phpolar\Core\PropertyAnnotation
 *
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadata
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataConfig
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataFactory
 * @uses \Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\NoopValidate
 * @uses \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 */
class ModelTest extends TestCase
{
    /**
     * @var <string, Phpolar\Phpolar\Core\Attributes\Config\AttributeConfigInterface>[]
     */
    protected static $attributesConfigMap;

    public static function setUpBeforeClass(): void
    {
        $attributesConfigFile = getcwd() . ATTRIBUTES_CONFIG_PATH;
        static::$attributesConfigMap = include $attributesConfigFile;
    }

    /**
     * @test
     */
    public function shouldKnowIfItEqualsAnotherModel()
    {
        $it = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "FAKE";
        };
        $other = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "FAKE";
        };
        $this->assertTrue($it->equals($other));
    }

    /**
     * @test
     */
    public function shouldKnowIfItDoesNotEqualAnotherModel()
    {
        $it = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "FAKE";
        };
        $other = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "DOES NOT MATCH";
        };
        $this->assertFalse($it->equals($other));
    }
}
