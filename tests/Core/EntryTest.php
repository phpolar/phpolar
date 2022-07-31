<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Entry
 * @covers \Efortmeyer\Polar\Core\PropertyAnnotation
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\Column
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @uses \Efortmeyer\Polar\Stock\Attributes\InputTypes
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadata
 * @uses \Efortmeyer\Polar\Core\Fields\TextField
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 */
class EntryTest extends TestCase
{
    public static function config()
    {
        $attributesConfigFile = $_SERVER["PWD"] . ATTRIBUTES_CONFIG_PATH;
        return [[require $attributesConfigFile]];
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnTheColumnNamesOfAllFields(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            /**
             * @var string
             * @Column(THIS IS A TEST)
             */
            public $property1 = "what";

            /**
             * @var string
             */
            public $property2 = "huh?";
        };
        $this->assertSame(["THIS IS A TEST", "Property2"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnThePropertyNamesOfAllFields(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1 = "what";

            /**
             * @var mixed
             */
            public $property2 = "huh?";
        };
        $this->assertSame(["property1", "property2"], $sut->getPropertyNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnThePropertyNamesOfAllFieldsWhenGivenAnAssociativeArray(Collection $configCollection)
    {
        $assocArray = ["property1" => "what", "property2" => "huh?"];
        $sut = new class($configCollection, $assocArray) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1 = "what";

            /**
             * @var mixed
             */
            public $property2 = "huh?";
        };
        $this->assertSame(["property1", "property2"], $sut->getPropertyNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnTheValuesOfAllFields(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1 = "what";

            /**
             * @var mixed
             */
            public $property2 = "huh?";
        };
        $this->assertSame(["what", "huh?"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldNotReturnTheStoredValuesOfAllFieldsWhenTheGivenArrayIsNotAssociative(Collection $configCollection)
    {
        $nonAssociativeArray = ["what", "huh?"];
        $sut = new class($configCollection, $nonAssociativeArray) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1;

            /**
             * @var mixed
             */
            public $property2;
        };

        $this->assertNotSame($nonAssociativeArray, $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnTheStoredValuesOfAllFieldsWhenGivenAnAssociativeArray(Collection $configCollection)
    {
        $assocArray = ["property1" => "what", "property2" => "huh?"];
        $sut = new class($configCollection, $assocArray) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1;

            /**
             * @var mixed
             */
            public $property2;
        };
        $values = array_values($assocArray);
        $this->assertSame($values, $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldReturnAllFields(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            /**
             * @var mixed
             */
            public $property1 = "what";
        };
        $expectedField = FieldMetadata::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("property1", $sut->property1);
        $fields = $sut->getFields();
        foreach ($fields as $field) {
            $this->assertSame($expectedField->getValue(), $field->getValue());
        }
    }
}
