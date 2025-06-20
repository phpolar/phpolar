<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;

/**
 * A collection of information that is intended to
 * reflect a past, current, or desired state of resources.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-3
 */
final class Representations
{
    /**
     * @param MimeType[] $representations
     */
    public function __construct(
        private readonly array $representations,
    ) {
    }

    /**
     * @param string[] $acceptable
     */
    public function contains(array $acceptable): bool
    {
        return array_any(
            $this->representations,
            static fn(MimeType $mimeType) => in_array($mimeType->value, $acceptable)
        );
    }
}
