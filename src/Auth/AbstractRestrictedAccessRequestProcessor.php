<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Phpolar\HttpRequestProcessor\RequestProcessorInterface;

/**
 * Represents a target object for a request route
 * that requires authorization.
 *
 * Objects that extend this class will have access
 * to metadata associated with the authenticated
 * user.  Route target objects that require
 * authorization should extend this class.
 *
 * @phan-file-suppress PhanWriteOnlyPublicProperty
 */
abstract class AbstractRestrictedAccessRequestProcessor implements RequestProcessorInterface
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
