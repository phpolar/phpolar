<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Tests\Stubs\EntityNameConfigured;
use Phpolar\Phpolar\Tests\Stubs\EntityNameNotConfigured;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use const Phpolar\Phpolar\Tests\Stubs\ENTITY_NAME_TEST_CASE;

/**
 * @covers \Phpolar\Phpolar\Model\EntityNameConfigurationTrait
 * @covers \Phpolar\Phpolar\Model\EntityName
 */
final class EntityNameConfigurationTraitTest extends TestCase
{
    /**
     * @testdox Shall return the configured entity name
     */
    public function test1()
    {
        $entity = new EntityNameConfigured();
        $actual = $entity->getName();
        $this->assertSame(ENTITY_NAME_TEST_CASE, $actual);
    }

    /**
     * @testdox Shall return the configured entity name
     */
    public function test2()
    {
        $entity = new EntityNameNotConfigured();
        $actual = $entity->getName();
        $this->assertSame(
            (new ReflectionClass(EntityNameNotConfigured::class))->getShortName(),
            $actual
        );
    }
}