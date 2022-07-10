<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;
use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Core\Rendering\Template;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\RequestHandler
 */
class RequestHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnInstanceOfTemplate()
    {
        $sut = new class() extends RequestHandler
        {
            public function testMethod() { return $this->getTemplateEngine(); }
            public function __invoke(TemplateContext $page, ?CollectionStorageInterface $storage = null): void
            {
                // noop
            }
        };
        $this->assertInstanceOf(Template::class, $sut->testMethod());
    }
}
