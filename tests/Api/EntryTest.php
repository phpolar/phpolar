<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Stock\Attributes\Config\ColumnKey;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Entry
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 * @covers \Efortmeyer\Polar\Stock\PropertyAnnotation
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
 * @uses \Efortmeyer\Polar\Stock\Field
 * @uses \Efortmeyer\Polar\Stock\TextField
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 */
class EntryTest extends TestCase
{
    public static function config()
    {
        $collection = new Collection();
        $collection->add(
            new ColumnKey(),
            new class(
                new ConstructorArgsPropertyName(),
                DefaultColumn::class,
                new ConstructorArgsPropertyName()
            ) extends AttributeConfig
            {
            }
        );
        return [[$collection]];
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
        $expectedField = Field::create("property1", $sut->property1, []);
        $fields = $sut->getFields();
        foreach ($fields as $field) {
            $this->assertSame($expectedField->getValue(), $field->getValue());
        }
    }
}
