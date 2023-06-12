<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Closure;
use Phpolar\Phpolar\Model\Model;
use Phpolar\Phpolar\Tests\Stubs\ModelStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;

#[CoversClass(ModelResolver::class)]
final class ModelResolverTest extends TestCase
{
    #[TestDox("Shall return a key-value pair with the argument name being the argument name of the model")]
    public function test1()
    {
        $emptyParsedBody = [];
        $expectedKey = "testClass";
        $reflectedObj = new class () {
            public function testMethod(#[Model] ?ModelStub $testClass = null)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), $emptyParsedBody);
        $resultPair = $sut->resolve();
        $this->assertArrayHasKey($expectedKey, $resultPair);
    }

    #[TestDox("Shall instantiate the object with the Model attribute and return it in a key-value pair")]
    public function test2()
    {
        $parsedBody = [
            "prop1" => "something",
            "prop2" => random_int(1, 200),
            "prop3" => "what",
        ];
        $reflectedObj = new class () {
            public function testMethod(#[Model] ?ModelStub $testClass = null)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), $parsedBody);
        $resultPair = $sut->resolve();
        $this->assertContainsOnlyInstancesOf(ModelStub::class, $resultPair);
    }

    #[TestDox("Shall return an empty array if no arguments have the Model attribute")]
    public function test3()
    {
        $reflectedObj = new class () {
            public function testMethod(ModelStub $testClass)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), null);
        $resultPair = $sut->resolve();
        $this->assertEmpty($resultPair);
    }

    #[TestDox("Shall return an empty array if union type hint is used")]
    public function test3b()
    {
        $reflectedObj = new class () {
            public function testMethod(ModelStub|string $testClass)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), null);
        $resultPair = $sut->resolve();
        $this->assertEmpty($resultPair);
    }

    #[TestDox("Shall return an empty array if intersection type hint is used")]
    public function test3c()
    {
        $reflectedObj = new class () {
            public function testMethod(ModelStub&Closure $testClass)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), null);
        $resultPair = $sut->resolve();
        $this->assertEmpty($resultPair);
    }

    #[TestDox("Shall throw an exception if the argument is not a subclass of AbstractModel")]
    public function test4()
    {
        $this->expectException(RuntimeException::class);
        $reflectedObj = new class () {
            public function testMethod(#[Model] ?object $testClass = null)
            {
            }
        };
        $sut = new ModelResolver(new ReflectionMethod($reflectedObj, "testMethod"), null);
        $sut->resolve();
    }
}
