<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ArrayAccess;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use TypeError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractModel::class)]
final class AbstractModelTest extends TestCase
{
    #[TestDox("Shall hydrate the model with the provided array")]
    public function test1()
    {
        $props = [
            "prop1" => 1,
            "prop2" => "some value",
        ];
        $model = new class ($props) extends AbstractModel {
            public int $prop1;
            public string $prop2;
        };
        foreach ($props as $propname => $val) {
            $this->assertSame($val, $model->$propname);
        }
    }

    #[TestDox("Shall not modify properties when an empty array is passed to the constructor")]
    public function test2()
    {
        $emptyArr = [];
        $model = new class ($emptyArr) extends AbstractModel {
            public int $prop1 = 0;
            public string $prop2 = "initial value";
        };
        $this->assertSame(0, $model->prop1);
        $this->assertSame("initial value", $model->prop2);
    }

    #[TestDox("Shall not add properties to the the model")]
    public function test3()
    {
        $nonExistingData = [
            "prop3" => "not there",
            "prop4" => "not there either",
        ];
        $model1 = new class ($nonExistingData) extends AbstractModel {
            public int $prop1 = 0;
            public string $prop2 = "initial value";
        };
        $this->assertSame(["prop1" => 0, "prop2" => "initial value"], get_object_vars($model1));
    }

    #[TestDox("Shall not add properties to the the model")]
    public function test4()
    {
        $nonExistingData = [
            "prop3" => "not there",
            "prop4" => "not there either",
        ];
        $model2 = new class ($nonExistingData) extends AbstractModel {
        };
        $this->assertSame([], get_object_vars($model2));
    }
    #[TestDox("Shall hydrate the model with the provided object")]
    public function test1b()
    {
        $props = (object) [
            "prop1" => 1,
            "prop2" => "some value",
        ];
        $model = new class ($props) extends AbstractModel {
            public int $prop1;
            public string $prop2;
        };
        foreach ($props as $propname => $val) {
            $this->assertSame($val, $model->$propname);
        }
    }

    #[TestDox("Shall not modify properties when an empty array is passed to the constructor when given an object")]
    public function test2b()
    {
        $emptyArr = (object) [];
        $model = new class ($emptyArr) extends AbstractModel {
            public int $prop1 = 0;
            public string $prop2 = "initial value";
        };
        $this->assertSame(0, $model->prop1);
        $this->assertSame("initial value", $model->prop2);
    }

    #[TestDox("Shall not add properties to the the model when given an object")]
    public function test3b()
    {
        $nonExistingData = (object) [
            "prop3" => "not there",
            "prop4" => "not there either",
        ];
        $model1 = new class ($nonExistingData) extends AbstractModel {
            public int $prop1 = 0;
            public string $prop2 = "initial value";
        };
        $this->assertSame(["prop1" => 0, "prop2" => "initial value"], get_object_vars($model1));
    }

    #[TestDox("Shall not add properties to the the model when given an object")]
    public function test4b()
    {
        $nonExistingData = (object) [
            "prop3" => "not there",
            "prop4" => "not there either",
        ];
        $model2 = new class ($nonExistingData) extends AbstractModel {
        };
        $this->assertSame([], get_object_vars($model2));
    }

    #[TestDox("Shall convert given string values from source object to the declared type of the target model")]
    public function test5()
    {
        $stringVals = (object) [
            "prop1" => "1",
            "prop2" => "1.0",
            "prop3" => "1",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public int $prop1;
            public float $prop2;
            public bool $prop3;
        };
        $this->assertSame(1, $model->prop1);
        $this->assertSame(1.0, $model->prop2);
        $this->assertTrue($model->prop3);
    }

    #[TestDox("Shall convert given string values from source array to the declared type of the target model")]
    public function test6()
    {
        $stringVals = [
            "prop1" => "1",
            "prop2" => "1.0",
            "prop3" => "1",
            "prop4" => "my string",
            "date" => "now",
            "dateImm" => "yesterday",
            "dateInt" => "100 years ago",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public int $prop1;
            public float $prop2;
            public bool $prop3;
            public string $prop4;
            public DateTime $date;
            public DateTimeImmutable $dateImm;
            public DateTimeInterface $dateInt;
        };
        $this->assertSame(1, $model->prop1);
        $this->assertSame(1.0, $model->prop2);
        $this->assertTrue($model->prop3);
        $this->assertSame("my string", $model->prop4);
        $this->assertInstanceOf(DateTime::class, $model->date);
        $this->assertInstanceOf(DateTimeImmutable::class, $model->dateImm);
        $this->assertInstanceOf(DateTimeInterface::class, $model->dateInt);
    }

    #[TestDox("Shall convert given string values from source array to the declared type of the target model")]
    public function test6bb()
    {
        $stringVals = (object) [
            "prop1" => "1",
            "prop2" => "1.0",
            "prop3" => "1",
            "prop4" => "my string",
            "date" => "now",
            "dateImm" => "yesterday",
            "dateInt" => "100 years ago",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public int $prop1;
            public float $prop2;
            public bool $prop3;
            public string $prop4;
            public DateTime $date;
            public DateTimeImmutable $dateImm;
            public DateTimeInterface $dateInt;
        };
        $this->assertSame(1, $model->prop1);
        $this->assertSame(1.0, $model->prop2);
        $this->assertTrue($model->prop3);
        $this->assertSame("my string", $model->prop4);
        $this->assertInstanceOf(DateTime::class, $model->date);
        $this->assertInstanceOf(DateTimeImmutable::class, $model->dateImm);
        $this->assertInstanceOf(DateTimeInterface::class, $model->dateInt);
    }

    #[TestDox("Shall throw an exception when intersection type is declared")]
    public function test7()
    {
        $this->expectException(TypeError::class);
        $stringVals = [
            "prop1" => "1",
        ];
        new class ($stringVals) extends AbstractModel {
            public DateTimeInterface&ArrayAccess $prop1;
        };
    }

    #[TestDox("Shall use the string value if the type is not declared")]
    public function test8()
    {
        $stringVals = [
            "prop1" => "1",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public $prop1;
        };
        $this->assertSame("1", $model->prop1);
    }

    #[TestDox("Shall convert given string values from source array to the declared type of the target model")]
    public function test6b()
    {
        $stringVals = [
            "prop1" => "1",
            "prop2" => "1.0",
            "prop3" => "1",
            "prop4" => "my string",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public int $prop1;
            public float $prop2;
            public bool $prop3;
            public string $prop4;
        };
        $this->assertSame(1, $model->prop1);
        $this->assertSame(1.0, $model->prop2);
        $this->assertTrue($model->prop3);
        $this->assertSame("my string", $model->prop4);
    }

    #[TestDox("Shall throw an exception when intersection type is declared")]
    public function test7b()
    {
        $this->expectException(TypeError::class);
        $stringVals = [
            "prop1" => "1",
        ];
        new class ($stringVals) extends AbstractModel {
            public DateTimeInterface&ArrayAccess $prop1;
        };
    }

    #[TestDox("Shall use the string value if the type is not declared")]
    public function test8b()
    {
        $stringVals = [
            "prop1" => "1",
        ];
        $model = new class ($stringVals) extends AbstractModel {
            public $prop1;
        };
        $this->assertSame("1", $model->prop1);
    }

    #[TestDox("Shall throw a type error if the type of the target property is non-scalar")]
    public function test10a()
    {
        $this->expectException(TypeError::class);
        $stringVals = [
            "prop1" => "1",
        ];
        new class ($stringVals) extends AbstractModel {
            public array $prop1;
        };
    }

    #[TestDox("Shall throw a type error if the type of the target property is non-scalar")]
    public function test10b()
    {
        $this->expectException(TypeError::class);
        $stringVals = (object) [
            "prop1" => "1",
        ];
        new class ($stringVals) extends AbstractModel {
            public array $prop1;
        };
    }

    #[TestDox("Shall support iteration over non-initialized public properties")]
    public function testa()
    {
        $sut = new class extends AbstractModel {
            public string $name;
            public string $address;
        };
        $iterated = false;
        foreach ($sut as $propName => $propVal) {
            $this->assertContains($propName, ["name", "address"]);
            $this->assertNull($propVal);
            $iterated = true;
        }
        $this->assertTrue($iterated, "Did not iterate over the object.");
    }
}
