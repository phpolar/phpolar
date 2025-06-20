<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Serializers\JsonSerializer;

final class JsonRepresentation implements RepresentationInterface
{
    public function __construct(
        private readonly mixed $resource,
    ) {
    }

    public function __toString(): string
    {
        return (new JsonSerializer())->serialize(
            $this->resource,
        );
    }
}
