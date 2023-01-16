<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageInterface;
use Phpolar\Phpolar\Api\Rendering\TemplateContext;
use Phpolar\Phpolar\Core\Rendering\Template;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\RequestHandler
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
            public function testMethod()
            {
                return $this->getTemplateEngine();
            }
            public function __invoke(TemplateContext $page, ?CollectionStorageInterface $storage = null): void
            {
                // noop
            }
        };
        $this->assertInstanceOf(Template::class, $sut->testMethod());
    }
}
