<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phpolar\Phpolar\Api\Attributes\Config\Collection;
use Phpolar\Phpolar\Core\Attributes\AttributeCollection;
use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Core\Fields\FieldMetadataConfig;
use Phpolar\Phpolar\Core\Fields\FieldMetadataFactory;
use Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue;
use Phpolar\Phpolar\Stock\Attributes\Column;
use Phpolar\Phpolar\Stock\Attributes\DateFormat;
use Phpolar\Phpolar\Stock\Attributes\Input;
use Phpolar\Phpolar\Stock\Attributes\Label;
use Phpolar\Phpolar\Stock\Attributes\MaxLength;
use Phpolar\Phpolar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Entry
 * @covers \Phpolar\Phpolar\Core\PropertyAnnotation
 * @covers \Phpolar\Phpolar\Api\Attributes\Config\Collection
 *
 * @covers \Phpolar\Phpolar\Core\Attributes\Attribute
 * @covers \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @covers \Phpolar\Phpolar\Core\Fields\FieldMetadataConfig
 * @covers \Phpolar\Phpolar\Core\Fields\FieldMetadataFactory
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @covers \Phpolar\Phpolar\Stock\Attributes\NoopValidate
 * @covers \Phpolar\Phpolar\Stock\Attributes\Input
 * @covers \Phpolar\Phpolar\Stock\Attributes\Label
 * @covers \Phpolar\Phpolar\Stock\Attributes\MaxLength
 * @uses \Phpolar\Phpolar\Core\Attributes\InputTypes
 * @uses \Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadata
 * @uses \Phpolar\Phpolar\Core\Fields\TextField
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @uses \Phpolar\Phpolar\Stock\Attributes\Column
 * @uses \Phpolar\Phpolar\Stock\Attributes\DateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
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
     * @group attributeTests
     */
    public function shouldUseTheValueOfThePropertyWhenConfiguredWithMaxLength(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[MaxLength(30)]
            public string $property1 = "WHATEVER";
        };
        $this->assertSame(["WHATEVER"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     * @group attributeTests
     */
    public function shouldUseTheValueOfThePropertyWhenCheckingMaxLength(Collection $configCollection)
    {
        $sut = new class($configCollection) extends Entry
        {
            #[MaxLength(3)]
            public string $property1 = "WHATEVER";
        };
        foreach ($sut->getFields() as $field) {
            foreach ($field->validators as $validator) {
                $this->assertFalse($validator->isValid());
            }
        }
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfFieldValuesWhenAllPropsUseAttributes(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            #[MaxLength(30)]
            public string $property1 = "what";

            #[MaxLength(30)]
            public string $property2 = "huh?";

            #[MaxLength(30)]
            public string $property3 = "again";
        };
        $this->assertSame(["what", "huh?", "again"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfColumnNamesWhenAllPropsUseAnnotations(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property2 = "huh?";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property3 = "again";
        };
        $this->assertSame(["Property1", "Property2", "Property3"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfPropNamesWhenAllPropsUseAnnotations(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property2 = "huh?";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property3 = "again";
        };
        $this->assertSame(["property1", "property2", "property3"], $sut->getPropertyNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfFieldValuesWhenAllPropsUseAnnotations(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property2 = "huh?";

            /**
             * @var string
             * MaxLength(30)
             */
            public $property3 = "again";
        };
        $this->assertSame(["what", "huh?", "again"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfColumnNamesWhenAllPropsNotConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            public string $property1 = "what";

            public string $property2 = "huh?";

            public string $property3 = "again";
        };
        $this->assertSame(["Property1", "Property2", "Property3"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfPropNamesWhenAllPropsNotConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            public string $property1 = "what";

            public string $property2 = "huh?";

            public string $property3 = "again";
        };
        $this->assertSame(["property1", "property2", "property3"], $sut->getPropertyNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfFieldValuesWhenAllPropsNotConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            public string $property1 = "what";

            public string $property2 = "huh?";

            public string $property3 = "again";
        };
        $this->assertSame(["what", "huh?", "again"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfColumnNamesWhenAllPropsMixConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            public string $property2 = "huh?";

            #[MaxLength(20)]
            public string $property3 = "again";
        };
        $this->assertSame(["Property1", "Property2", "Property3"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfPropNamesWhenAllPropsMixConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            public string $property2 = "huh?";

            #[MaxLength(20)]
            public string $property3 = "again";
        };
        $this->assertSame(["property1", "property2", "property3"], $sut->getPropertyNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfFieldValuesWhenAllPropsMixConfigured(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            /**
             * @var string
             * MaxLength(30)
             */
            public $property1 = "what";

            public string $property2 = "huh?";

            #[MaxLength(20)]
            public string $property3 = "again";
        };
        $this->assertSame(["what", "huh?", "again"], $sut->getFieldValues());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfColumnNamesWhenAllPropsUseAttributes(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            #[MaxLength(30)]
            public string $property1 = "what";

            #[MaxLength(30)]
            public string $property2 = "huh?";

            #[MaxLength(30)]
            public string $property3 = "again";
        };
        $this->assertSame(["Property1", "Property2", "Property3"], $sut->getColumnNames());
    }

    /**
     * @test
     * @dataProvider config
     */
    public function shouldMaintainOrderOfPropNamesWhenAllPropsUseAttributes(Collection $config)
    {
        $sut = new class($config) extends Entry
        {
            #[MaxLength(30)]
            public string $property1 = "what";

            #[MaxLength(30)]
            public string $property2 = "huh?";

            #[MaxLength(30)]
            public string $property3 = "again";
        };
        $this->assertSame(["property1", "property2", "property3"], $sut->getPropertyNames());
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
