<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\SizeNotConfigured;
use Phpolar\Phpolar\Validation\MaxLength;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Size::class)]
#[CoversClass(SizeConfigurationTrait::class)]
final class SizeConfigurationTraitTest extends TestCase
{
    #[TestDox("Shall return the given argument as size")]
    public function test1()
    {
        $entity = new class ()
        {
            use SizeConfigurationTrait;

            #[Size(5)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    #[TestDox("Shall return the given max length as size")]
    public function test2()
    {
        $entity = new class ()
        {
            use SizeConfigurationTrait;

            #[MaxLength(5)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    #[TestDox("Shall give Size attribute configuration preference over MaxLength configuration")]
    public function test3()
    {
        $entity = new class ()
        {
            use SizeConfigurationTrait;

            #[Size(5)]
            #[MaxLength(8)]
            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertSame(5, $actual);
    }

    #[TestDox("Shall return SizeNotConfigured instance when Size attribute does not exist")]
    public function test4()
    {
        $entity = new class ()
        {
            use SizeConfigurationTrait;

            public $someProp;
        };
        $actual = $entity->getSize("someProp");
        $this->assertInstanceOf(SizeNotConfigured::class, $actual);
    }
}
