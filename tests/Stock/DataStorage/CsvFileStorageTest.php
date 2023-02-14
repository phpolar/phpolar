<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\DataStorage;

use Phpolar\Phpolar\Api\Attributes\Config\Collection;
use Phpolar\Phpolar\Api\Model;
use Phpolar\Phpolar\Tests\Extensions\PhpolarTestCaseExtension;
use Phpolar\Phpolar\Tests\Mocks\ModelSubclass;
use Phpolar\Phpolar\Tests\Mocks\NonMatchingPropModel;
use InvalidArgumentException;
use IteratorIterator;
use RuntimeException;

/**
 * @covers \Phpolar\Phpolar\Stock\DataStorage\CsvFileStorage
 * @covers \Phpolar\Phpolar\Api\Attributes\Config\Collection
 * @covers \Phpolar\Phpolar\Api\Model
 * @covers \Phpolar\Phpolar\Core\Attributes\Attribute
 *
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\TypeTag
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @uses \Phpolar\Phpolar\Core\Entry
 * @uses \Phpolar\Phpolar\Core\PropertyAnnotation
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
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
 */
class CsvFileStorageTest extends PhpolarTestCaseExtension
{
    /**
     * @var Model
     */
    protected $record;

    /**
     * @var CsvFileStorage
     */
    protected $sut;

    /**
     * @var Collection
     */
    protected static $attributesConfigMap;

    protected static $fakeCsvFilePath;

    protected const CLASS_NAME_THAT_DOES_NOT_DERIVE_FROM_MODEL = IteratorIterator::class;

    public static function setUpBeforeClass(): void
    {
        $attributesConfigFile = getcwd() . \ATTRIBUTES_CONFIG_PATH;
        static::$attributesConfigMap = include $attributesConfigFile;
    }

    protected function setUp(): void
    {
        static::$fakeCsvFilePath = self::getTestFileName(".csv");
        $this->record = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var mixed
             */
            public $property1 = "FAKE VALUE";

            /**
             * @var mixed
             */
            public $property2 = "ANOTHER FAKE VALUE";

            /**
             * @var mixed
             */
            public $property3 = "AGAIN... ANOTHER FAKE VALUE";
        };
        $sut = new CsvFileStorage(static::$attributesConfigMap, static::$fakeCsvFilePath);
        $sut->save($this->record);
        $this->sut = $sut;
    }

    protected function tearDown(): void
    {
        if (file_exists(static::$fakeCsvFilePath) === true) {
            unlink(static::$fakeCsvFilePath);
        }
    }

    /**
     * @test
     */
    public function shouldSaveDataToFile()
    {
        $fileContents = file_get_contents(static::$fakeCsvFilePath);
        $recordAsArray = get_object_vars($this->record);
        $this->assertStringArrayContainStrings($recordAsArray, $fileContents);
    }

    /**
     * @test
     */
    public function shouldReturnACollectionOfInstancesOfTheGivenClass()
    {
        $resultList = $this->sut->list(ModelSubclass::class);
        $this->assertContainsOnlyInstancesOf(ModelSubclass::class, $resultList);
    }

    /**
     * @test
     */
    public function shouldReturnAnEmptyCollectionWhenTheFileDoesNotExist()
    {
        // need the file handle to close before creating the new file
        // See https://www.php.net/manual/en/function.unlink
        unlink(static::$fakeCsvFilePath);
        $this->sut->__destruct();
        $newFile = self::getTestFileName(".csv");
        $sut = new CsvFileStorage(static::$attributesConfigMap, $newFile);
        $resultList = $sut->list(ModelSubclass::class);
        unlink($newFile);
        $this->assertEmpty($resultList);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenGivenClassNameThatDoesNotDeriveFromModelClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sut->list(self::CLASS_NAME_THAT_DOES_NOT_DERIVE_FROM_MODEL);
    }

    /**
     * @test
     */
    public function shouldNotAddHeaderIfFileIsNotEmpty()
    {
        $anotherRecord  = new class(static::$attributesConfigMap) extends Model
        {
            /**
             * @var string
             */
            public $headerThatDoesNotExist = "FAKE STRING";
        };
        $this->sut->save($anotherRecord);
        $fileContents = file_get_contents(static::$fakeCsvFilePath);
        $this->assertStringNotContainsString("headerThatDoesNotExist", $fileContents);
    }

    /**
     * @test
     */
    public function shouldDoNothingIfTheObjectDoesNotHaveProperties()
    {
        $recordWithoutProps = new class(static::$attributesConfigMap) extends Model
        {
        };
        $fileContentsBeforeSave = file_get_contents(static::$fakeCsvFilePath);
        $this->sut->save($recordWithoutProps);
        $fileContentsAfterSave = file_get_contents(static::$fakeCsvFilePath);
        $this->assertSame($fileContentsBeforeSave, $fileContentsAfterSave);
    }

    /**
     * @test
     */
    public function shouldThrowAnExceptionIfModelDoesNotMatchSerializedRepresentation()
    {
        $this->expectException(RuntimeException::class);
        $this->sut->list(NonMatchingPropModel::class);
    }
}
