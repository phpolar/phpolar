<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\InputTypes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\Input
 */
class InputTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\InputTestData::type
     */
    public function shouldReturnTheGivenText(InputTypes $givenType)
    {
        $sut = new Input($givenType);
        $actualResult = $sut();
        $this->assertEquals($givenType->value, $actualResult);
    }
}
