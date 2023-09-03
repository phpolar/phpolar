<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormControlTypeDetectionTrait::class)]
final class FormControlTypeDetectionTraitTest extends TestCase
{
    #[TestDox("Shall detect form control types")]
    public function test1()
    {
        $model = new class()
        {
            use FormControlTypeDetectionTrait;

            public int $numProp;
            public float $floatProp;
            public string $strProp;
            public bool $checkboxProp = false;
            public array $selectProp = [
                "opt1",
                "opt2",
                "opt3",
                "opt4",
            ];
            public DateTimeInterface $dateProp1;
            public DateTime $dateProp2;
            public DateTimeImmutable $dateProp3;
            public Closure $funcProp;
            public string | int | bool $unionProp;
            public string | int | bool | array $invalidUnionProp1;
            public int | bool | array $invalidUnionProp2;
            public $noValueNotDeclaredProp;
            public $numNotDeclaredProp = 2;
            public $floatNotDeclaredProp = 2e9;
            public $boolNotDeclaredProp = false;
            public $arrayNotDeclaredProp = [
                "opt1",
                "opt2",
                "opt3",
                "opt4",
            ];
            public $dateNotDeclaredProp1;
            public $dateNotDeclaredProp2;
            public $dateNotDeclaredProp3;
            public $nullNotDeclaredProp;
            public $funcNotDeclaredProp;
        };
        $model->dateNotDeclaredProp1 = new DateTime();
        $model->dateNotDeclaredProp2 = new DateTimeImmutable();
        $myDateImpl = new class() extends DateTimeImmutable
        {
        };
        $model->dateNotDeclaredProp3 = $myDateImpl;
        $model->nullNotDeclaredProp = null;
        $model->funcNotDeclaredProp = fn () => null;
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("unionProp"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("invalidUnionProp1"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("invalidUnionProp2"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("numProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("floatProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("strProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("checkboxProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp1"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp2"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateProp3"));
        $this->assertInstanceOf(FormControlTypes::Select::class, $model->getFormControlType("selectProp"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("funcProp"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("noValueNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("nullNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Invalid::class, $model->getFormControlType("funcNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("numNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("floatNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("boolNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Select::class, $model->getFormControlType("arrayNotDeclaredProp"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateNotDeclaredProp1"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateNotDeclaredProp2"));
        $this->assertInstanceOf(FormControlTypes::Input::class, $model->getFormControlType("dateNotDeclaredProp3"));
    }
}
