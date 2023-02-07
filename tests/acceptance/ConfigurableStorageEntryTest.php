<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Model\Column;
use Phpolar\Phpolar\Model\Size;
use Phpolar\Phpolar\Model\ColumnNameTrait;
use Phpolar\Phpolar\Model\DataTypeDetectionTrait;
use Phpolar\Phpolar\Model\SizeConfigurationTrait;
use Phpolar\Phpolar\Tests\Stubs\EntityNameConfigured;
use Phpolar\StorageDriver\StorageDriverInterface;
use Phpolar\StorageDriver\TypeName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Stringable;

use const \Phpolar\Phpolar\Tests\ENTITY_NAME_TEST_CASE;

final class ConfigurableStorageEntryTest extends TestCase
{
    #[Test]
    #[TestDox("Should configure column names")]
    public function criterion1()
    {
        $model1 = new class()
        {
            use ColumnNameTrait;

            #[Column("test")]
            public string $someProp;
        };
        $model2 = new class()
        {
            use ColumnNameTrait;

            #[Column]
            public string $someProp;
        };
        $model3 = new class()
        {
            use ColumnNameTrait;

            public string $someProp;
        };

        foreach (
            [
                [$model1, "test", "someProp"],
                [$model2, "SomeProp", "someProp"],
                [$model3, "SomeProp", "someProp"],
            ] as [$model, $expectedColumnName, $propName]
        ) {
            $this->assertSame(
                $expectedColumnName,
                $model->getColumnName($propName)
            );
        }
    }

    #[Test]
    #[TestDox("Should detect data types")]
    public function criterion2()
    {
        $model = new class()
        {
            use DataTypeDetectionTrait;
            public string $someProp;
        };
        $expectedColumnDataTypeString = <<<SQL
        VARCHAR
        SQL;
        $storageDriverStub = new class() implements StorageDriverInterface
        {
            public function getDataType(TypeName $typeName): Stringable
            {
                return new class() implements Stringable
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

    #[Test]
    #[TestDox("Should allow configuration of size")]
    public function criterion3()
    {
        $entity = new class()
        {
            use SizeConfigurationTrait;

            #[Size(5)]
            public string $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    #[Test]
    #[TestDox("Should have optional table name configuration")]
    public function criterion4()
    {
        $entity = new EntityNameConfigured();
        $actual = $entity->getName();
        $this->assertSame(ENTITY_NAME_TEST_CASE, $actual);
    }
}
