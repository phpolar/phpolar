<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestProcessorExecutor::class)]
final class RequestProcessorExecutorTest extends TestCase
{
    #[TestDox("Shall call the request processor's process method with no arguments when the argument array is empty")]
    #[TestWith([[]])]
    public function testa(array $emptyArgumentArray)
    {
        $requestProcessorMock = $this->createMock(RoutableInterface::class);

        $requestProcessorMock
            ->expects($this->once())
            ->method("process")
            ->with()
            ->willReturn("");

        $sut = new RequestProcessorExecutor();

        $sut->execute($requestProcessorMock, $emptyArgumentArray);
    }

    #[TestDox("Shall call the request processor's process method with the given arguments when the argument array is not empty")]
    #[TestWith([["id" => "123", "name" => "John"], "id", "name"])]
    public function testb(array $argumentArray, string $key0, string $key1)
    {
        $requestProcessor = new class () implements RoutableInterface {
            public function process(string $id = "", string $name = ""): array|bool|int|null|object|string
            {
                return sprintf("%s_%s", $id, $name);
            }
        };

        $sut = new RequestProcessorExecutor();

        $result = $sut->execute($requestProcessor, $argumentArray);

        $this->assertSame($argumentArray[$key0] . "_" . $argumentArray[$key1], $result);
    }
}
