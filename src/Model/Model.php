<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Attribute;

/**
 * Marks a method argument as being a model.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Model
{
}
