<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\FieldMetadataConfig;
use Efortmeyer\Polar\Core\Fields\FieldMetadataFactory;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Stock\Attributes\Label;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Entry
 * @covers \Efortmeyer\Polar\Core\PropertyAnnotation
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 *
 * @covers \Efortmeyer\Polar\Core\Attributes\Attribute
 * @covers \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 * @covers \Efortmeyer\Polar\Core\Fields\FieldMetadataConfig
 * @covers \Efortmeyer\Polar\Core\Fields\FieldMetadataFactory
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @covers \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @covers \Efortmeyer\Polar\Stock\Attributes\Input
 * @covers \Efortmeyer\Polar\Stock\Attributes\Label
 * @covers \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Core\Attributes\InputTypes
 * @uses \Efortmeyer\Polar\Core\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadata
 * @uses \Efortmeyer\Polar\Core\Fields\TextField
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @uses \Efortmeyer\Polar\Stock\Attributes\Column
 * @uses \Efortmeyer\Polar\Stock\Attributes\DateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 */
class EntryTest extends TestCase
{
    private static function getFactory(AttributeCollection $attrs): FieldMetadataFactory
    {
        $className = $attrs->getFieldClassName();
        return new FieldMetadataFactory(
            new $className(),
            new FieldMetadataConfig($attrs),
        );
    }

    public static function config()
    {
        $attributesConfigFile = getcwd() . ATTRIBUTES_CONFIG_PATH;
        return [[require $attributesConfigFile]];
    }

    /**
     * @test
     * @dataProvider config
     * @group annotationTests
     */
    public function shouldReturnTheColumnNamesOfAllFieldsUsingAnnotations(Collection $configCollection)
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
     * @group attributeTests
     */
    public function shouldReturnTheColumnNamesOfAllFieldsUsingAttributes(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[Column("THIS IS A TEST")]
            public string $property1 = "what";
        };
        $this->assertSame(["THIS IS A TEST"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     * @group attributeTests
     */
    public function shouldConfigureColumnsWithAndWithoutAttributes(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[Column("THIS IS A TEST")]
            public string $property1 = "what";

            public string $property2 = "what";
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
     * @group attributeTests
     */
    public function shouldReturnThePropertyNamesOfAllFieldsWhenUsingAttributes(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[Column("SOMETHING")]
            #[Label("SOMETHING ELSE")]
            #[Input(InputTypes::Text)]
            public $property1 = "what";

            #[Column("SOMETHING")]
            #[Label("SOMETHING ELSE")]
            #[Input(InputTypes::Textarea)]
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
     * @group attributeTests
     */
    public function shouldReturnTheValuesOfAllFieldsWhenConfiguredWithAttributes(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[Label("SOMETHING")]
            #[Column("SOMETHING ELSE")]
            public string $property1 = "what";

            #[Column("ANYTHING")]
            public string $property2 = "huh?";
        };
        $this->assertSame(["what", "huh?"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     * @group attributeTests
     */
    public function shouldReturnTheValuesOfAllDateFieldsWhenConfiguredWithAttributes(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[Label("SOMETHING")]
            #[Column("SOMETHING ELSE")]
            #[DateFormat("Y/m/d")]
            #[AutomaticDateValue]
            public DateTimeInterface $property1;
        };
        $this->assertSame([date("Y/m/d")], $sut->getFieldValues());
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
        $expectedField = self::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("property1", $sut->property1);
        $fields = $sut->getFields();
        foreach ($fields as $field) {
            $this->assertSame($expectedField->getValue(), $field->getValue());
        }
    }
}
