<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageFactory;
use Phpolar\Phpolar\Api\Rendering\TemplateContext;
use Phpolar\Phpolar\Stock\AppConfig\InMemoryAppConfig;
use Phpolar\Phpolar\Stock\DataStorage\CsvFileStorage;
use Phpolar\Phpolar\Tests\Mocks\StorageStub;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \Phpolar\Phpolar\Api\App
 *
 * @uses \Phpolar\Phpolar\Api\Attributes\Config\Collection
 * @uses \Phpolar\Phpolar\Stock\AppConfig\InMemoryAppConfig
 * @uses \Phpolar\Phpolar\Stock\DataStorage\CsvFileStorage
 * @uses \Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig
 * @uses \Phpolar\Phpolar\Stock\Attributes\Config\InputKey
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

    /**
     * @test
     */
    public function shouldSetAppConfigAndStorageWhenUsingDefaultCreation()
    {
        $_SERVER["REQUEST_URI"] = "DOES NOT EXIST";
        $sut = App::create($_SERVER["REQUEST_URI"]);
        $reflectionClass = new ReflectionClass($sut);
        $reflectionProperty = $reflectionClass->getProperty("storage");
        $reflectionProperty->setAccessible(true);
        $storagePropertyValue = $reflectionProperty->getValue($sut);
        $reflectionProperty = $reflectionClass->getProperty("appConfig");
        $reflectionProperty->setAccessible(true);
        $appConfigPropertyValue = $reflectionProperty->getValue($sut);
        $this->assertInstanceOf(CsvFileStorage::class, $storagePropertyValue);
        $this->assertInstanceOf(InMemoryAppConfig::class, $appConfigPropertyValue);
    }
}
