<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

final class HtmlRepresentation implements RepresentationInterface
{
    private readonly string $resource;

    public function __construct(
        mixed $resource,
    ) {
        if (is_string($resource) === false) {
            throw new InvalidHtmlResponseException();
        }
        $this->resource = $resource;
    }

    public function __toString(): string
    {
        return $this->resource;
    }
}
