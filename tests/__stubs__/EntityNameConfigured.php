<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Phpolar\Phpolar\EntityName;
use Phpolar\Phpolar\EntityNameConfigurationTrait;

const ENTITY_NAME_TEST_CASE = "MY TEST";

#[EntityName(ENTITY_NAME_TEST_CASE)]
final class EntityNameConfigured
{
    use EntityNameConfigurationTrait;
}