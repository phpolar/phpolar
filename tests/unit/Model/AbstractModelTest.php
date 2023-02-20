<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

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
    #[TestDox("Shall hydrate the model with the provided array")]
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

    #[TestDox("Shall not modify properties when an empty array is passed to the constructor")]
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

    #[TestDox("Shall not add properties to the the model")]
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

    #[TestDox("Shall not add properties to the the model")]
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
}
