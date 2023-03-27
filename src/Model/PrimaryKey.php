<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Attribute;

/**
 * Provides support for configuring properties as primary keys.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class PrimaryKey
{
}
