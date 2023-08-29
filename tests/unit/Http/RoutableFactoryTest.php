<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Tests\Fakes\FakeRoutable;
use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(RoutableFactory::class)]
final class RoutableFactoryTest extends TestCase
{
    #[TestDox("Shall throw an exception if the provided string is not a classname of an instance of RoutableInterface")]
    public function testa()
    {
        $this->expectException(RuntimeException::class);
        new RoutableFactory("NOT_A_CLASSNAME_OF_INSTANCE_OF_ROUTABLE_INTERFACE");
    }

    #[TestDox("Shall create an instance of the provided RoutableInterface classname")]
    public function testb()
    {
        $sut = new RoutableFactory(FakeRoutable::class);
        $result = $sut->createInstance();
        $this->assertInstanceOf(RoutableInterface::class, $result);
    }
}
