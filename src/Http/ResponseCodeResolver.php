<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Phpolar\Phpolar\Http\Status\ClientError\BadRequest;
use Phpolar\Phpolar\Http\Status\ClientError\Forbidden;
use Phpolar\Phpolar\Http\Status\ClientError\NotFound;
use Phpolar\Phpolar\Http\Status\ClientError\Unauthorized;

/**
 * Determines the appropriate HTTP response code
 * for a given situation.
 */
final readonly class ResponseCodeResolver
{
    /**
     * Resolve the appropriate HTTP response code.
     */
    public function resolve(HttpResponseCode $default, mixed $resource): HttpResponseCode
    {
        return match (true) {
            $resource instanceof NotFound => HttpResponseCode::NotFound,
            $resource instanceof BadRequest => HttpResponseCode::BadRequest,
            $resource instanceof Unauthorized => HttpResponseCode::Unauthorized,
            $resource instanceof Forbidden => HttpResponseCode::Forbidden,
            default => $default,
        };
    }
}
