<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer\Http;

use Closure;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
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
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(ErrorHandler::class)]
final class ErrorHandlerTest extends TestCase
{
    public const FAKE_TEMPLATE = "FAKE TEMPLATE";

    #[TestDox("Shall return default error message if the error template file does not exist")]
    public function test1a()
    {
        $renderingAlgo = new class () implements TemplatingStrategyInterface
        {
            public function getAlgorithm(): Closure
            {
                return fn () => new FileNotFound();
            }
        };
        $config = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($config);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[TemplateEngine::class] = new TemplateEngine(
            $renderingAlgo,
            new Binder(),
            new Dispatcher(),
        );
        $sut = new ErrorHandler(
            ResponseCode::NOT_FOUND,
            "Not Found",
            $container
        );
        $this->assertSame("<h1>Not Found</h1>", $sut->handle(new RequestStub())->getBody()->getContents());
    }

    /**
     * @tesdox Shall return default error message if the a bind error occurs.
     */
    public function test1b()
    {
        $renderingAlgo = new class () implements TemplatingStrategyInterface
        {
            public function getAlgorithm(): Closure
            {
                return fn () => "";
            }
        };
        /**
         * @var Stub&Binder $binderStub
         */
        $binderStub = $this->createStub(Binder::class);
        $binderStub->method("bind")->willReturn(false);
        $config = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($config);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[TemplateEngine::class] = new TemplateEngine(
            $renderingAlgo,
            $binderStub,
            new Dispatcher(),
        );
        $sut = new ErrorHandler(
            ResponseCode::INTERNAL_SERVER_ERROR,
            "Internal Server Error",
            $container,
        );
        $this->assertSame("<h1>Internal Server Error</h1>", $sut->handle(new RequestStub())->getBody()->getContents());
    }

    #[TestDox("Shall return the template engine results if file exists.")]
    public function test2()
    {
        $renderingAlgo = new class () implements TemplatingStrategyInterface
        {
            public function getAlgorithm(): Closure
            {
                return fn () => ErrorHandlerTest::FAKE_TEMPLATE;
            }
        };
        $config = new ContainerConfigurationStub();
        $container = new ConfigurableContainerStub($config);
        $config[ResponseFactoryInterface::class] = new ResponseFactoryStub();
        $config[StreamFactoryInterface::class] = new StreamFactoryStub();
        $config[TemplateEngine::class] = new TemplateEngine(
            $renderingAlgo,
            new Binder(),
            new Dispatcher(),
        );
        $sut = new ErrorHandler(
            ResponseCode::UNAUTHORIZED,
            "Unauthorized",
            $container,
        );
        $prevCwd = getcwd();
        chdir("tests/__templates__");
        $responseBodyContents = $sut->handle(new RequestStub())->getBody()->getContents();
        chdir($prevCwd);
        $this->assertSame(ErrorHandlerTest::FAKE_TEMPLATE, $responseBodyContents);
    }
}
