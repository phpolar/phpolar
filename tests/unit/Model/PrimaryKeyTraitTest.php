<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(PrimaryKeyTrait::class)]
#[CoversClass(PrimaryKey::class)]
final class PrimaryKeyTraitTest extends TestCase
{
    #[TestDox("Shall return the value of the string property configured as the primary key")]
    public function testaa()
    {
        $givenKeyVal = "123";
        $sut = new class ($givenKeyVal) {
            use PrimaryKeyTrait;

            #[PrimaryKey]
            public string $id;

            public function __construct(string $id)
            {
                $this->id = $id;
            }
        };
        $this->assertSame($sut->getPrimaryKey(), $givenKeyVal);
    }

    #[TestDox("Shall return the value of the integer property configured as the primary key")]
    public function testab()
    {
        $givenKeyVal = 123;
        $sut = new class ($givenKeyVal) {
            use PrimaryKeyTrait;

            #[PrimaryKey]
            public int $id;

            public function __construct(int $id)
            {
                $this->id = $id;
            }
        };
        $this->assertSame($sut->getPrimaryKey(), $givenKeyVal);
    }

    #[TestDox("Shall return the empty string for string property configured as the primary key if no value is set")]
    public function testb()
    {
        $sut = new class () {
            use PrimaryKeyTrait;

            #[PrimaryKey]
            public string $id;
        };

        $this->assertEmpty($sut->getPrimaryKey());
    }

    #[TestDox("Shall return zero for integer property configured as the primary key if no value is set")]
    public function testc()
    {
        $sut = new class () {
            use PrimaryKeyTrait;

            #[PrimaryKey]
            public int $id;
        };

        $this->assertSame(0, $sut->getPrimaryKey());
    }

    #[TestDox("Shall return null for non-string, non-integer property configured as the primary key if no value is set")]
    public function testd()
    {
        $sut = new class () {
            use PrimaryKeyTrait;

            #[PrimaryKey]
            public bool $id;
        };

        $this->assertNull($sut->getPrimaryKey());
    }

    #[TestDox("Shall return null when no property is configured as the primary key")]
    public function teste()
    {
        $sut = new class () {
            use PrimaryKeyTrait;

            public string $id;
        };

        $this->assertNull($sut->getPrimaryKey());
    }
}
