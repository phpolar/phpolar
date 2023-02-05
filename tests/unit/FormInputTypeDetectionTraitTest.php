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
 * @covers \Phpolar\Phpolar\FormInputTypeDetectionTrait
 */
final class FormInputTypeDetectionTraitTest extends TestCase
{
    /**
     * @test
     * @testdox Shall detect form input types
     */
    public function test1()
    {
        $model = new class()
        {
            use FormInputTypeDetectionTrait;

            #[Hidden]
            public string $hiddenProp;
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
            public $strNotDeclaredProp = "str";
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
        $this->assertInstanceOf(InputTypes::Text::class, $model->getInputType("unionProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("invalidUnionProp1"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("invalidUnionProp2"));
        $this->assertInstanceOf(InputTypes::Number::class, $model->getInputType("numProp"));
        $this->assertInstanceOf(InputTypes::Number::class, $model->getInputType("floatProp"));
        $this->assertInstanceOf(InputTypes::Text::class, $model->getInputType("strProp"));
        $this->assertInstanceOf(InputTypes::Hidden::class, $model->getInputType("hiddenProp"));
        $this->assertInstanceOf(InputTypes::Checkbox::class, $model->getInputType("checkboxProp"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateProp1"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateProp2"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateProp3"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("selectProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("funcProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("noValueNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("nullNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("funcNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Text::class, $model->getInputType("strNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Number::class, $model->getInputType("numNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Number::class, $model->getInputType("floatNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Checkbox::class, $model->getInputType("boolNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Invalid::class, $model->getInputType("arrayNotDeclaredProp"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateNotDeclaredProp1"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateNotDeclaredProp2"));
        $this->assertInstanceOf(InputTypes::Date::class, $model->getInputType("dateNotDeclaredProp3"));
    }
}
