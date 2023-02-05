<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phpolar\Phpolar\Core\InputTypes;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class ConfigurableFormFieldTest extends TestCase
{
    /**
     * @test
     * @testdox Shall support configurable form labels
     */
    public function criterion1()
    {
        $model = new class() extends AbstractModel
        {
            public const A_LABEL = "something";

            #[Label]
            public string $prop1 = "whatever";

            #[Label("another name")]
            public int $prop2 = 1;

            #[Label(self::A_LABEL)]
            public int $prop3 = 1;
        };
        $this->assertSame("Prop1", $model->getLabel("prop1"));
        $this->assertSame("another name", $model->getLabel("prop2"));
        $this->assertSame($model::A_LABEL, $model->getLabel("prop3"));
    }

    /**
     * @test
     * @testdox Shall support configurable form validation
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FormValidationDataProvider::getTestCases()
     */
    public function criterion2(bool $expected, string $validationType, AbstractModel $model)
    {
        $this->assertSame($expected, $model->isValid(), "{$validationType} valildation failed");
    }

    /**
     * @test
     * @testdox Shall support configurable form validation alerts
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FormFieldErrorMessageDataProvider::invalidPropertyTestCases()
     */
    public function criterion3(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertSame($expectedMessage, $model->getFieldErrorMessage($fieldName));
    }

    /**
     * @test
     * @testdox Shall support form field type detection
     */
    public function criterion4()
    {
        $model = new class()
        {
            use FormControlTypeDetectionTrait;

            public int $numProp;

            public string $strProp;

            public array $selectProp = [
                "opt1",
                "opt2",
                "opt3",
                "opt4",
            ];

            public bool $checkboxProp = false;

            public DateTimeInterface $dateProp1;
            public DateTime $dateProp2;
            public DateTimeImmutable $dateProp3;

            public Closure $funcProp;
        };
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("numProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("strProp"));
        $this->assertInstanceOf(FormControlTypes::Select::class, $model->getFormControlType("selectProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("checkboxProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp1"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp3"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp2"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("funcProp"));
    }

    /**
     * @test
     * @testdox Shall support converting detected input types to string
     * @testWith ["datetime-local", "dateProp"]
     *           ["number", "numProp"]
     *           ["checkbox", "checkboxProp"]
     *           ["text", "strProp"]
     */
    public function criterion5(string $expected, string $propName)
    {
        $model = new class()
        {
            use FormInputTypeDetectionTrait;

            public int $numProp;
            public string $strProp;
            public bool $checkboxProp = false;
            public DateTimeInterface $dateProp;
        };
        $this->assertSame($expected, $model->getInputType($propName)->asString());
    }

    /**
     * @test
     * @testdox Shall support hidden form field configuration
     */
    public function criterion6()
    {
        $model = new class()
        {
            use FormInputTypeDetectionTrait;

            #[Hidden]
            public string $prop;
        };
        $this->assertInstanceOf(InputTypes::Hidden::class, $model->getInputType("prop"));
    }
}
