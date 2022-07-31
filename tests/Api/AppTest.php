<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageFactory;
use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Tests\Mocks\StorageStub;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \Efortmeyer\Polar\Api\App
 *
 * @uses \Efortmeyer\Polar\Api\Attributes\Config\Collection
 * @uses \Efortmeyer\Polar\Api\InMemoryAppConfig
 * @uses \Efortmeyer\Polar\Stock\DataStorage\CsvFileStorage
 * @uses \Efortmeyer\Polar\Core\Attributes\Config\AttributeConfig
 * @uses \Efortmeyer\Polar\Stock\Attributes\Config\InputKey
 */
class AppTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDisplayNotFoundTextWhenRequestUriDoesNotExistInRouteConfig()
    {
        $routeConfig = [];
        $_SERVER["REQUEST_URI"] = "DOES NOT EXIST";
        $sut = App::configure($_SERVER["REQUEST_URI"], new InMemoryAppConfig());
        $sut->setUpRoutes($routeConfig);
        $sut->run();
        $this->expectOutputString("Not Found");
    }

    /**
     * @test
     */
    public function shouldDisplayTextFromGivenHandler()
    {
        $routeConfig = [
            "/" => function () {
                echo "Bang!";
            }
        ];
        $givenStorage = $this->createStub(StorageStub::class);
        /**
         * @var Stub|CollectionStorageFactory
         */
        $storageFactory = $this->createStub(CollectionStorageFactory::class);
        $storageFactory->method("getStorage")->willReturn($givenStorage);
        $givenRootTemplate = $this->createStub(TemplateContext::class);
        $templateFactory = function () use ($givenRootTemplate) {
            return $givenRootTemplate;
        };
        $_SERVER["REQUEST_URI"] = "/";
        $sut = App::configure($_SERVER["REQUEST_URI"], new InMemoryAppConfig());
        $sut->setUpRoutes($routeConfig);
        $sut->setUpRootTemplate($templateFactory);
        $sut->setUpStorage($storageFactory);
        $sut->run();
        $this->expectOutputString("Bang!");
    }

    /**
     * @test
     */
    public function shouldSetPage()
    {
        $givenRootTemplate = $this->createStub(TemplateContext::class);
        $templateFactory = fn () => $givenRootTemplate;
        $routeConfig = [];
        $_SERVER["REQUEST_URI"] = "DOES NOT EXIST";
        $sut = App::configure($_SERVER["REQUEST_URI"], new InMemoryAppConfig());
        $sut->setUpRoutes($routeConfig);
        $sut->setUpRootTemplate($templateFactory);
        $reflectionClass = new ReflectionClass($sut);
        $reflectionProperty = $reflectionClass->getProperty("page");
        $reflectionProperty->setAccessible(true);
        $pagePropertyValue = $reflectionProperty->getValue($sut);
        $this->assertEquals($givenRootTemplate, $pagePropertyValue);
    }

    /**
     * @test
     */
    public function shouldSetStorage()
    {
        $givenStorage = $this->createStub(StorageStub::class);
        /**
         * @var Stub|CollectionStorageFactory
         */
        $storageFactory = $this->createStub(CollectionStorageFactory::class);
        $storageFactory->method("getStorage")->willReturn($givenStorage);
        $routeConfig = [];
        $_SERVER["REQUEST_URI"] = "DOES NOT EXIST";
        $sut = App::configure($_SERVER["REQUEST_URI"], new InMemoryAppConfig());
        $sut->setUpRoutes($routeConfig);
        $sut->setUpStorage($storageFactory);
        $reflectionClass = new ReflectionClass($sut);
        $reflectionProperty = $reflectionClass->getProperty("storage");
        $reflectionProperty->setAccessible(true);
        $storagePropertyValue = $reflectionProperty->getValue($sut);
        $this->assertEquals($givenStorage, $storagePropertyValue);
    }
}
