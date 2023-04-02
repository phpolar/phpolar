<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phpolar\Phpolar\Model\InputTypes;
use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\FormControlTypes;
use Phpolar\Phpolar\Model\FormControlTypeDetectionTrait;
use Phpolar\Phpolar\Model\FormInputTypeDetectionTrait;
use Phpolar\Phpolar\Model\Hidden;
use Phpolar\Phpolar\Model\Label;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ConfigurableFormFieldTest extends TestCase
{
    #[Test]
    #[TestDox("Shall support configurable form labels")]
    public function criterion1()
    {
        $model = new class () extends AbstractModel
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

    #[Test]
    #[TestDox("Shall support form field type detection")]
    public function criterion4()
    {
        $model = new class ()
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

    #[Test]
    #[TestWith(["datetime-local", "dateProp"])]
    #[TestWith(["number", "numProp"])]
    #[TestWith(["checkbox", "checkboxProp"])]
    #[TestWith(["text", "strProp"])]
    #[TestDox("Shall support converting detected \$expected input types to string")]
    public function criterion5(string $expected, string $propName)
    {
        $model = new class ()
        {
            use FormInputTypeDetectionTrait;

            public int $numProp;
            public string $strProp;
            public bool $checkboxProp = false;
            public DateTimeInterface $dateProp;
        };
        $this->assertSame($expected, $model->getInputType($propName)->asString());
    }

    #[Test]
    #[TestDox("Shall support hidden form field configuration")]
    public function criterion6()
    {
        $model = new class ()
        {
            use FormInputTypeDetectionTrait;

            #[Hidden]
            public string $prop;
        };
        $this->assertInstanceOf(InputTypes::Hidden::class, $model->getInputType("prop"));
    }
}
