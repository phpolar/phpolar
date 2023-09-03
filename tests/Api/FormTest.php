<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use DateTimeImmutable;
use Efortmeyer\Polar\Api\Model;
use Efortmeyer\Polar\Api\UIElements\DateFormControl;
use Efortmeyer\Polar\Api\UIElements\ErrorBanner;
use Efortmeyer\Polar\Api\UIElements\HiddenFormControl;
use Efortmeyer\Polar\Api\UIElements\SuccessBanner;
use Efortmeyer\Polar\Api\UIElements\TextAreaFormControl;
use Efortmeyer\Polar\Api\UIElements\TextFormControl;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Tests\Mocks\StorageStub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\Form
 *
 * @uses \Efortmeyer\Polar\Api\Model
 * @uses \Efortmeyer\Polar\Api\UIElements\ErrorBanner
 * @uses \Efortmeyer\Polar\Api\UIElements\SuccessBanner
 * @uses \Efortmeyer\Polar\Api\UIElements\FormControl
 * @uses \Efortmeyer\Polar\Api\UIElements\TextAreaFormControl
 * @uses \Efortmeyer\Polar\Api\UIElements\TextFormControl
 * @uses \Efortmeyer\Polar\Api\Attributes\Config\Collection
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Entry
 * @uses \Efortmeyer\Polar\Core\Attributes\Attribute
 * @uses \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadata
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataConfig
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataFactory
 * @uses \Efortmeyer\Polar\Core\PropertyAnnotation
 * @uses \Efortmeyer\Polar\Core\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 */
class FormTest extends TestCase
{
    /**
     * @var <string, Efortmeyer\Polar\Core\Attributes\Config\AttributeConfigInterface>[]
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
    public function shouldReturnTheModel()
    {
        $it = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "FAKE";
        };
        $sut = new Form($it);
        $this->assertEquals($it, $sut->getModel());
    }

    /**
     * @test
     */
    public function shouldSetBannerToSuccessBannerWhenModelIsValid()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertInstanceOf(SuccessBanner::class, $sut->banner);
    }

    /**
     * @test
     */
    public function shouldSetBannerToErrorBannerWhenModelIsInvalid()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             * @MaxLength(2)
             */
            public $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertInstanceOf(ErrorBanner::class, $sut->banner);
    }

    /**
     * @test
     */
    public function shouldReturnTextFormControls()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             * @Input(text)
             */
            public $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(TextFormControl::class, $sut->getTextInputs());
    }

    /**
     * @test
     */
    public function shouldReturnTextFormControlsWhenConfiguredWithNativeAttribute()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            #[Input(InputTypes::Text)]
            public string $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(TextFormControl::class, $sut->getTextInputs());
    }

    /**
     * @test
     */
    public function shouldReturnTextAreaFormControls()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             * @Input(textarea)
             */
            public $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(TextAreaFormControl::class, $sut->getTextAreaInputs());
    }

    /**
     * @test
     */
    public function shouldReturnTextAreaFormControlsWhenConfiguredWithNativeAttributes()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            #[Input(InputTypes::Textarea)]
            public string $property1 = "FAKE";
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(TextAreaFormControl::class, $sut->getTextAreaInputs());
    }

    /**
     * @test
     */
    public function shouldReturnDateFormControls()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var DateTimeImmutable
             * @Input(date)
             */
            public $property1;
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(DateFormControl::class, $sut->getDateInputs());
    }

    /**
     * @test
     */
    public function shouldReturnDateFormControlsWhenConfiguredWithNativeAttributes()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            #[Input(InputTypes::Date)]
            public DateTimeImmutable $property1;
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(DateFormControl::class, $sut->getDateInputs());
    }

    /**
     * @test
     */
    public function shouldReturnHiddenFormControls()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var DateTimeImmutable
             * @Input(date)
             * @AutomaticDateValue
             */
            public $property1;
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(HiddenFormControl::class, $sut->getHiddenInputs());
    }

    /**
     * @test
     */
    public function shouldReturnHiddenFormControlsWhenConfiguredWithNativeAttributes()
    {
        $model = new class(static::$attributesConfigMap) extends Model
        {
            #[Input(InputTypes::Date)]
            #[AutomaticDateValue]
            public DateTimeImmutable $property1;
        };
        $storageStub = $this->createStub(StorageStub::class);
        $sut = new Form($model);
        $sut->submit($storageStub);
        $this->assertContainsOnlyInstancesOf(HiddenFormControl::class, $sut->getHiddenInputs());
    }
}
