<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\DataStorage;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Api\Model;
use Efortmeyer\Polar\Tests\Extensions\PolarTestCaseExtension;
use Efortmeyer\Polar\Tests\Mocks\ModelSubclass;
use Efortmeyer\Polar\Tests\Mocks\NonMatchingPropModel;
use InvalidArgumentException;
use IteratorIterator;
use RuntimeException;

/**
 * @covers \Efortmeyer\Polar\Stock\DataStorage\CsvFileStorage
 * @covers \Efortmeyer\Polar\Api\Attributes\Config\Collection
 * @covers \Efortmeyer\Polar\Api\Model
 * @covers \Efortmeyer\Polar\Core\Attributes\Attribute
 *
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Core\Entry
 * @uses \Efortmeyer\Polar\Core\PropertyAnnotation
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
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 */
class CsvFileStorageTest extends PolarTestCaseExtension
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
        $attributesConfigFile = getcwd() . ATTRIBUTES_CONFIG_PATH;
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
        $sut = new CsvFileStorage(static::$fakeCsvFilePath, static::$attributesConfigMap);
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
        $sut = new CsvFileStorage($newFile, static::$attributesConfigMap);
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
