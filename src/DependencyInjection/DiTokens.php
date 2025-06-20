<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

/**
 * Contains dependency injection tokens.
 *
 * @suppress PhanUnreferencedPublicClassConstant
 */
final class DiTokens
{
    public const CSRF_CHECK_MIDDLEWARE = "CSRF_CHECK_MIDDLEWARE";
    public const CSRF_RESPONSE_FILTER_MIDDLEWARE = "CSRF_RESPONSE_FILTER_MIDDLEWARE";
    public const RESPONSE_EMITTER = "RESPONSE_EMITTER";
    public const AUTHENTICATED_ROUTING_HANDLER = "AUTHENTICATED_ROUTING_HANDLER";
    public const UNAUTHORIZED_HANDLER = "UNAUTHORIZED_HANDLER";
    public const NOT_FOUND_HANDLER = "NOT_FOUND_HANDLER";
    public const NOOP_AUTH_CHECKER = "NOOP_AUTH_CHECKER";
    public const NOOP_PROPERTY_INJECTOR = "NOOP_PROPERTY_INJECTOR";
    public const NOOP_ROUTABLE_RESOLVER = "NOOP_ROUTABLE_RESOLVER";
}
