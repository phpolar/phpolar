<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

/**
 * Has the ability to determine
 * if a request has been authenticated.
 */
interface AuthenticatorInterface
{
    /**
     * Returns user information from
     * the authenticated session.
     */
    public function getCredentials(): ?object;
}
