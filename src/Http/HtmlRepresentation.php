<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;

final class HtmlRepresentation implements RepresentationInterface
{
    private readonly string $resource;
    private readonly MimeType $mimeType;

    public function __construct(
        mixed $resource,
    ) {
        $this->mimeType = MimeType::TextHtml;
        if (is_string($resource) === false) {
            throw new InvalidHtmlResponseException();
        }
        $this->resource = $resource;
    }

    public function getMimeType(): string
    {
        return $this->mimeType->value;
    }

    public function __toString(): string
    {
        return $this->resource;
    }
}
