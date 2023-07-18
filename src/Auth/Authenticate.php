<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Attribute;
use Phpolar\Phpolar\RoutableInterface;

/**
 * Provides an authentication mechanism
 * for objects that handle requests.
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Authenticate
{
    /**
     * Return the target `Routable` when the request
     * has been authenticated.  Otherwise, return
     * the fallback `Routable`.
     */
    public function getResolvedRoutable(
        AbstractProtectedRoutable $target,
        AuthenticatorInterface $authenticator,
    ): RoutableInterface | false {
        return $authenticator->isAuthenticated() === false ? false : $target->withUser((object) $authenticator->getUser());
    }
}
