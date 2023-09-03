<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use DateTimeImmutable;
use Phpolar\Phpolar\Api\Model;
use Phpolar\Phpolar\Api\UIElements\DateFormControl;
use Phpolar\Phpolar\Api\UIElements\ErrorBanner;
use Phpolar\Phpolar\Api\UIElements\HiddenFormControl;
use Phpolar\Phpolar\Api\UIElements\SuccessBanner;
use Phpolar\Phpolar\Api\UIElements\TextAreaFormControl;
use Phpolar\Phpolar\Api\UIElements\TextFormControl;
use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue;
use Phpolar\Phpolar\Stock\Attributes\Input;
use Phpolar\Phpolar\Tests\Mocks\StorageStub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\Form
 *
 * @uses \Phpolar\Phpolar\Api\Model
 * @uses \Phpolar\Phpolar\Api\UIElements\ErrorBanner
 * @uses \Phpolar\Phpolar\Api\UIElements\SuccessBanner
 * @uses \Phpolar\Phpolar\Api\UIElements\FormControl
 * @uses \Phpolar\Phpolar\Api\UIElements\TextAreaFormControl
 * @uses \Phpolar\Phpolar\Api\UIElements\TextFormControl
 * @uses \Phpolar\Phpolar\Api\Attributes\Config\Collection
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @uses \Phpolar\Phpolar\Core\Entry
 * @uses \Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadata
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataConfig
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataFactory
 * @uses \Phpolar\Phpolar\Core\PropertyAnnotation
 * @uses \Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Attributes\NoopValidate
 * @uses \Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue
 * @uses \Phpolar\Phpolar\Stock\Attributes\Input
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
 */
class FormTest extends TestCase
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
