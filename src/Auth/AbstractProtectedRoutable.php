<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Phpolar\Routable\RoutableInterface;

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
     * Create a `User` from the given user object
     * and assign it to the user property.
     */
    public function withUser(object $user): self
    {
        $copy = clone $this;
        $copy->user = new User(
            avatarUrl: $user->avatarUrl ?? "",
            email: $user->email ?? "",
            name: $user->name ?? "",
            nickname: $user->nickname ?? "",
            picture: $user->picture ?? null,
        );
        return $copy;
    }
}
