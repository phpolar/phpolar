<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Phpolar\Phpolar\RoutableInterface;

/**
 * Represents an authenticated request delegate
 * that contains the credentials of an authenticated
 * user.
 */
abstract class AbstractProtectedRoutable implements RoutableInterface
{
    /**
     * Contains credentials for an authenticated user
     */
    public User $user;

    /**
     * Create a `User` from the given session
     * and assign it to the user property.
     */
    public function withUser(object $session): self
    {
        $copy = clone $this;
        $copy->user = new User(
            avatarUrl: $session->user->avatarUrl ?? "",
            email: $session->user->email ?? "",
            name: $session->user->name ?? "",
            nickname: $session->user->nickname ?? "",
            picture: $session->user->picture ?? null,
        );
        return $copy;
    }
}
