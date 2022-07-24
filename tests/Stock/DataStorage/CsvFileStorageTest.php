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
 *
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\TypeTag
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @uses \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @uses \Efortmeyer\Polar\Stock\Entry
 * @uses \Efortmeyer\Polar\Stock\Field
 * @uses \Efortmeyer\Polar\Stock\PropertyAnnotation
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 *
 * @testdox CsvFileStorage
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

    protected const CLASS_NAME_THAT_DOES_NOT_DERIVE_FROM_MODEL = IteratorIterator::class;

    public static function setUpBeforeClass(): void
    {
        $attributesConfigFile = $_SERVER["PWD"] . ATTRIBUTES_CONFIG_PATH;
        static::$attributesConfigMap = include $attributesConfigFile;
    }

    protected function setUp(): void
    {
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
        $sut = new CsvFileStorage(FAKE_CSV_FILE_PATH, static::$attributesConfigMap);
        $sut->save($this->record);
        $this->sut = $sut;
    }

    protected function tearDown(): void
    {
        if (file_exists(FAKE_CSV_FILE_PATH) === true) {
            unlink(FAKE_CSV_FILE_PATH);
        }
    }

    /**
     * @test
     */
    public function shouldSaveDataToFile()
    {
        $fileContents = file_get_contents(FAKE_CSV_FILE_PATH);
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
        unlink(FAKE_CSV_FILE_PATH);
        $resultList = $this->sut->list(ModelSubclass::class);
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
        $fileContents = file_get_contents(FAKE_CSV_FILE_PATH);
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
        $fileContentsBeforeSave = file_get_contents(FAKE_CSV_FILE_PATH);
        $this->sut->save($recordWithoutProps);
        $fileContentsAfterSave = file_get_contents(FAKE_CSV_FILE_PATH);
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
