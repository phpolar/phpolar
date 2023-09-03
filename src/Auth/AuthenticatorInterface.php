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
     * Returns user credentials from
     * the authenticated session.
     */
    public function getCredentials(): ?object;

    /**
     * Returns user information from
     * the authenticated session.
     * @return array<string,string>|null
     */
    public function getUser(): ?array;

    /**
     * Determines if the session is
     * authenticated.
     */
    public function isAuthenticated(): bool;
}
