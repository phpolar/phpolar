<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Phpolar\Phpolar\Model\EntityName;
use Phpolar\Phpolar\Model\EntityNameConfigurationTrait;
use const Phpolar\Phpolar\Tests\ENTITY_NAME_TEST_CASE;

#[EntityName(ENTITY_NAME_TEST_CASE)]
final class EntityNameConfigured
{
    use EntityNameConfigurationTrait;
}