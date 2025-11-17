<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http\Status\ClientError;

/**
 * Indicates that the origin server
 * server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing) did not find a current representation for the target resource or is not willing to disclose that one exists.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.1
 */
final readonly class BadRequest
{
}
