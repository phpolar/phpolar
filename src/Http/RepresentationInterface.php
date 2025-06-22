<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Stringable;

/**
 * A representation of the requested resource.
 */
interface RepresentationInterface extends Stringable
{
    public function getMimeType(): string;
}
