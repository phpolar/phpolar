<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\ColumnNameTrait
 * @covers \Phpolar\Phpolar\Column
 */
final class ColumnNameTraitTest extends TestCase
{
    /**
     * @testdox Shall return title cased property name when not configured
     */
    public function test1()
    {
        $entity = new class()
        {
            use ColumnNameTrait;

            public $someProp;
        };
        $this->assertSame("SomeProp", $entity->getColumnName("someProp"));
    }

    /**
     * @testdox Shall return title cased property name when column name not supplied
     */
    public function test2()
    {
        $entity = new class()
        {
            use ColumnNameTrait;

            #[Column]
            public $someProp;
        };
        $this->assertSame("SomeProp", $entity->getColumnName("someProp"));
    }

    /**
     * @testdox Shall return configured column name
     */
    public function test3()
    {
        $entity = new class()
        {
            use ColumnNameTrait;

            #[Column("whatever")]
            public $someProp;
        };
        $this->assertSame("whatever", $entity->getColumnName("someProp"));
    }
}