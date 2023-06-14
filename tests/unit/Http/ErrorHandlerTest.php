<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Closure;
use Phpolar\HttpCodes\ResponseCode;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use Phpolar\Phpolar\Core\Formats;
use Phpolar\Phpolar\Tests\Stubs\ConfigurableContainerStub;
use Phpolar\Phpolar\Tests\Stubs\ContainerConfigurationStub;
use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\FileNotFound;
use Phpolar\PurePhp\TemplateEngine;
use Phpolar\PurePhp\TemplatingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
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
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
        $config[TemplateEngine::class] = new TemplateEngine(
            $renderingAlgo,
            new Binder(),
            new Dispatcher(),
        );
        $sut = new ErrorHandler(
            ResponseCode::IM_A_TEAPOT,
            "I'm a teapot",
            $container
        );
        $this->assertSame(sprintf(Formats::ErrorText->value, "I'm a teapot"), $sut->handle(new RequestStub())->getBody()->getContents());
    }

    #[TestDox("Shall return default error message if the a bind error occurs")]
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
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
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
        $config[StreamFactoryInterface::class] = new StreamFactoryStub("+w");
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
