<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer\Http;

use Closure;
use Phpolar\Phpolar\Tests\Stubs\RequestStub;
use Phpolar\Phpolar\Tests\Stubs\ResponseFactoryStub;
use Phpolar\Phpolar\Tests\Stubs\StreamFactoryStub;
use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\FileNotFound;
use Phpolar\PhpTemplating\TemplateEngine;
use Phpolar\PhpTemplating\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Error401Handler::class)]
final class Error401HandlerTest extends TestCase
{
    public const FAKE_TEMPLATE = "FAKE TEMPLATE";

    #[TestDox("Shall return default error message if the error template file does not exist")]
    public function test1a()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $renderingAlgo = new class () implements TemplatingStrategyInterface {
            public function getAlgorithm(): Closure
            {
                return fn() => new FileNotFound();
            }
        };
        $sut = new Error401Handler(
            $responseFactory,
            $streamFactory,
            new TemplateEngine(
                $renderingAlgo,
                new Binder(),
                new Dispatcher(),
            )
        );
        $this->assertSame(Error401Handler::DEFAULT_ERROR_MSG, $sut->handle(new RequestStub())->getBody()->getContents());
    }

    // /**
    //  * @tesdox Shall return default error message if the a bind error occurs.
    //  */
    // public function test1b()
    // {
    //     $responseFactory = new ResponseFactoryStub();
    //     $streamFactory = new StreamFactoryStub();
    //     $renderingAlgo = new class() implements TemplatingStrategyInterface {
    //         public function getAlgorithm(): Closure
    //         {
    //             return fn() => "";
    //         }
    //     };
    //     /**
    //      * @var Stub&Binder $binderStub
    //      */
    //     $binderStub = $this->createStub(Binder::class);
    //     $binderStub->method("bind")->willReturn(false);
    //     $sut = new Error401Handler(
    //         $responseFactory,
    //         $streamFactory,
    //         new TemplateEngine(
    //             $renderingAlgo,
    //             $binderStub,
    //             new Dispatcher(),
    //         )
    //     );
    //     $this->assertSame(Error401Handler::DEFAULT_ERROR_MSG, $sut->handle(new RequestStub())->getBody()->getContents());
    // }

    #[TestDox("Shall return the default error message if the error template file exists.")]
    public function test2()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new StreamFactoryStub();
        $renderingAlgo = new class () implements TemplatingStrategyInterface {
            public function getAlgorithm(): Closure
            {
                return fn() => Error401HandlerTest::FAKE_TEMPLATE;
            }
        };
        $sut = new Error401Handler(
            $responseFactory,
            $streamFactory,
            new TemplateEngine(
                $renderingAlgo,
                new Binder(),
                new Dispatcher(),
            )
        );
        $this->assertSame(Error401HandlerTest::FAKE_TEMPLATE, $sut->handle(new RequestStub())->getBody()->getContents());
    }
}
