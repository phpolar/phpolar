<?php

declare(strict_types=1);

use Efortmeyer\Polar\Api\Model;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\Model
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 * @covers \Efortmeyer\Polar\Core\Entry
 * @covers \Efortmeyer\Polar\Core\PropertyAnnotation
 *
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Core\Attributes\Attribute
 * @uses \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadata
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataConfig
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataFactory
 * @uses \Efortmeyer\Polar\Core\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 */
class ModelTest extends TestCase
{
    /**
     * @var <string, Efortmeyer\Polar\Core\Attributes\Config\AttributeConfigInterface>[]
     */
    protected static $attributesConfigMap;

    public static function setUpBeforeClass(): void
    {
        $attributesConfigFile = $_SERVER["PWD"] . ATTRIBUTES_CONFIG_PATH;
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
