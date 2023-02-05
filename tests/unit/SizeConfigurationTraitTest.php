<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Core\SizeNotConfigured;
use Phpolar\Phpolar\Size;
use Phpolar\Phpolar\Validation\MaxLength;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\SizeConfigurationTrait
 * @covers \Phpolar\Phpolar\Size
 */
final class SizeConfigurationTraitTest extends TestCase
{
    /**
     * @testdox Shall return the given argument as size
     */
    public function test1()
    {
        $entity = new class()
        {
            use SizeConfigurationTrait;

            #[Size(5)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    /**
     * @testdox Shall return the given max length as size
     */
    public function test2()
    {
        $entity = new class()
        {
            use SizeConfigurationTrait;

            #[MaxLength(5)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    /**
     * @testdox Shall give Size attribute configuration preference over MaxLength configuration
     */
    public function test3()
    {
        $entity = new class()
        {
            use SizeConfigurationTrait;

            #[Size(5)]
            #[MaxLength(8)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    /**
     * @testdox Shall return SizeNotConfigured instance when Size attribute does not exist
     */
    public function test4()
    {
        $entity = new class()
        {
            use SizeConfigurationTrait;

            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertInstanceOf(SizeNotConfigured::class, $actual);
    }
}