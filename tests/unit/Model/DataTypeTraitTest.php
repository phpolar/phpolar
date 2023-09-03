<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use DateTime;
use DateTimeImmutable;
use Stringable;
use Phpolar\StorageDriver\DataTypeUnknown;
use Phpolar\StorageDriver\StorageDriverInterface;
use Phpolar\StorageDriver\TypeName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(DataTypeDetectionTrait::class)]
final class DataTypeTraitTest extends TestCase
{
    #[TestDox("Shall generate column parameter string from declared property type")]
    public function test1()
    {
        $model = new class ()
        {
            use DataTypeDetectionTrait;

            public string $someProp;
        };
        $expectedColumnDataTypeString = <<<SQL
        VARCHAR
        SQL;
        $storageDriverStub = new class () implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class () implements Stringable
                {
                    public function __toString(): string
                    {
                        return "VARCHAR";
                    }
                };
            }
        };
        $actual = $model->getDataType("someProp", $storageDriverStub);
        $this->assertSame($expectedColumnDataTypeString, (string) $actual);
    }

    #[TestDox("Shall return unknown data type when type is unknown")]
    public function test2()
    {
        $model = new class ()
        {
            use DataTypeDetectionTrait;

            public object $someProp;
        };
        $storageDriverStub = new class () implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class () implements Stringable {
                    public function __toString(): string
                    {
                        return "";
                    }
                };
            }
        };
        $actual = $model->getDataType("someProp", $storageDriverStub);
        $this->assertInstanceOf(DataTypeUnknown::class, $actual);
    }

    #[TestDox("Shall return expected data type when property is initialized and type is undeclared")]
    public function test3()
    {
        $varCharStub = new class () implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class () implements Stringable
                {
                    public function __toString(): string
                    {
                        return "VARCHAR";
                    }
                };
            }
        };
        $dateTimeStub = new class () implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class () implements Stringable
                {
                    public function __toString(): string
                    {
                        return "DATETIME";
                    }
                };
            }
        };
        foreach (
            [
                [new DateTime(), "DATETIME", $dateTimeStub],
                [new DateTimeImmutable(), "DATETIME", $dateTimeStub],
                ["anyString", "VARCHAR", $varCharStub],
            ] as [$propertyValue, $expectedColumnDataTypeString, $storageDriverStub]
        ) {
            $model = new class ($propertyValue)
            {
                use DataTypeDetectionTrait;

                public function __construct(public $someProp)
                {
                }
            };
            $actual = $model->getDataType("someProp", $storageDriverStub);
            $this->assertSame($expectedColumnDataTypeString, (string) $actual);
        }
    }

    #[TestDox("Shall return unknown data type when property is an non-DateTime object")]
    public function test5()
    {
        $model = new class ((object) [])
        {
            use DataTypeDetectionTrait;

            public function __construct(public $someProp)
            {
            }
        };
        $storageDriverStub = new class () implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class () implements Stringable {
                    public function __toString(): string
                    {
                        return "";
                    }
                };
            }
        };
        $actual = $model->getDataType("someProp", $storageDriverStub);
        $this->assertInstanceOf(DataTypeUnknown::class, $actual);
    }

    #[TestDox("Shall return unknown data type when property is an array, callable, or closure")]
    public function test6()
    {
        $testCases = [
            [],
        ];
        array_walk(
            $testCases,
            function ($propertyValue) {
                $model = new class ($propertyValue)
                {
                    use DataTypeDetectionTrait;

                    public function __construct(public $someProp)
                    {
                    }
                };
                $storageDriverStub = new class () implements StorageDriverInterface
                {
                    public function getDataType(TypeName $typeName): Stringable|DataTypeUnknown
                    {
                        return new DataTypeUnknown();
                    }
                };
                $actual = $model->getDataType("someProp", $storageDriverStub);
                $this->assertInstanceOf(DataTypeUnknown::class, $actual);
            },
        );
    }
}
