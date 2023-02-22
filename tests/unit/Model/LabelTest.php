<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Tests\DataProviders\LabelDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Label::class)]
#[CoversClass(LabelFormatTrait::class)]
final class LabelTest extends TestCase
{
    #[DataProviderExternal(LabelDataProvider::class, "getLabelTestCases")]
    #[TestDox("Shall return formatted text using property name")]
    public function test1(string $expected, string $propName, AbstractModel $sut)
    {
        $this->assertSame($expected, $sut->getLabel($propName));
    }

    #[DataProviderExternal(LabelDataProvider::class, "getUnconfiguredPropertyTestCases")]
    #[TestDox("Shall return formatted text when property is not configured")]
    public function test2(string $expected, string $propName, AbstractModel $sut)
    {
        $this->assertSame($expected, $sut->getLabel($propName));
    }

    #[DataProviderExternal(LabelDataProvider::class, "getConfiguredLabelTestCases")]
    #[TestDox("Shall return configured label for property")]
    public function test3(string $expected, string $propName, AbstractModel $sut)
    {
        $this->assertSame($expected, $sut->getLabel($propName));
    }
}
