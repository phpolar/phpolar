<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Rendering;

use Phpolar\Phpolar\Tests\Comparables\NestedXSSFix;
use Phpolar\Phpolar\Tests\Comparables\NestedXSSFixEnd;
use Phpolar\Phpolar\Tests\Comparables\NestedXSSHack;
use Phpolar\Phpolar\Tests\Comparables\NestedXSSHackEnd;
use Phpolar\Phpolar\Tests\Extensions\PhpolarTestCaseExtension;
use Phpolar\Phpolar\Tests\Mocks\SerializableMock;
use Phpolar\Phpolar\Tests\Mocks\StringableMock;

use Serializable;
use stdClass;
use Stringable;

/**
 * @covers \Phpolar\Phpolar\Core\Rendering\HtmlEncoder
 *
 * @testdox Serialize::htmlEncode
 */
class HtmlEncoderTest extends PhpolarTestCaseExtension
{
    /**
     * @var resource
     */
    protected $testResource;

    protected function setUp(): void
    {
        $this->testResource = fopen("php://stdout", "a+");
    }

    protected function tearDown(): void
    {
        fclose($this->testResource);
    }

    public function stringableTestCases()
    {
        $stub = new StringableMock();
        $expectedResult = "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;";
        return [
            [$stub, $expectedResult]
        ];
    }

    public function serializableTestCases()
    {
        $stub = new SerializableMock();
        $expectedResult = "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;";
        return [
            [$stub, $expectedResult]
        ];
    }

    public function emptyStringReturnTestCases()
    {
        return [
            [null],
            [
                function () {
                }
            ],
            [$this->testResource],
        ];
    }

    public function stringTestCases()
    {
        return [
            ["<a href='javascript:alert(document.cookie)'>hacked</a>", "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;"],
        ];
    }

    public function booleanTestCases()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    public function numberTestCases()
    {
        return array_map(
            fn ($num) => [$num, $num],
            array_merge(
                range(-1, random_int(5, 20)),
                range(-1.4, random_int(5, 10)),
            )
        );
    }

    public function nestedObjectTestCases()
    {
        $nestedObject = new class() extends NestedXSSHack
        {
            public function __construct()
            {
                $this->child = new class() extends NestedXSSHack
                {
                    public function __construct()
                    {
                        $this->child = new class() extends NestedXSSHackEnd
                        {
                        };
                    }
                };
            }
        };
        $expectedObject = new class() extends NestedXSSFix
        {
            public function __construct()
            {
                $this->child = new class() extends NestedXSSFix
                {
                    public function __construct()
                    {
                        $this->child = new class() extends NestedXSSFixEnd
                        {
                        };
                    }
                };
            }
        };
        return [
            [$nestedObject, $expectedObject],
        ];
    }

    public function iterableTestCases()
    {
        return [
            [array_keys(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5)), array_values(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5))],
        ];
    }

    /**
     * @test
     * @dataProvider stringTestCases
     */
    public function shouldSanitizeStrings(string $givenValue, string $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $expectedObject = clone $object;
        $expectedObject->prop = $expectedResult;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $this->assertEquals($actualResult, $expectedObject);
    }

    /**
     * @test
     * @dataProvider booleanTestCases
     */
    public function shouldReturnBoolean(bool $givenValue, bool $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $this->assertEquals($actualResult, $object);
    }

    /**
     * @test
     * @dataProvider numberTestCases
     */
    public function shouldReturnNumber($givenValue, $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $this->assertEquals($actualResult, $object);
    }

    /**
     * @test
     * @dataProvider stringableTestCases
     */
    public function shouldReturnExpectedResultWhenGivenInstanceOfStringable(Stringable $givenValue, $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $object->prop = $expectedResult;
        $this->assertEquals($object, $actualResult);
    }

    /**
     * @test
     * @dataProvider serializableTestCases
     */
    public function shouldReturnExpectedResultWhenGivenInstanceOfSerializable(Serializable $givenValue, $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $object->prop = $expectedResult;
        $this->assertEquals($object, $actualResult);
    }

    /**
     * @test
     * @dataProvider emptyStringReturnTestCases
     */
    public function shouldReturnEmptyWhenGivenCertainValues($givenValue)
    {
        $object = new stdClass();
        $object->prop = $givenValue;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $object->prop = "";
        $this->assertEquals($actualResult, $object);
    }

    /**
     * @test
     */
    public function shouldReturnObjectWhenGivenObjectWithoutProperties()
    {
        $object = new stdClass();
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $this->assertEquals($actualResult, $object);
    }

    /**
     * @test
     * @dataProvider nestedObjectTestCases
     */
    public function shouldReturnSanitizedNestedPropertiesWhenGivenANestedObject(object $givenNestedObject, object $expectedResult)
    {
        $actualResult = (new HtmlEncoder())->encodeProperties($givenNestedObject);
        $this->assertObjectDeepEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     * @dataProvider iterableTestCases
     */
    public function shouldReturnSanitizeValuesWhenGivenAnIterable(iterable $givenIterable, $expectedResult)
    {
        $object = new stdClass();
        $object->prop = $givenIterable;
        $actualResult = (new HtmlEncoder())->encodeProperties($object);
        $object->prop = $expectedResult;
        $this->assertEquals($object, $actualResult);
    }
}
