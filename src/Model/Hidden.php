<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Attribute;

/**
 * Use to denote a class's property as a hidden form input.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Hidden
{
}
