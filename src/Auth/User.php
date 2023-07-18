<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

/**
 * Represents an authenticated user.
 */
final class User
{
    public function __construct(
        public string $name,
        public string $nickname,
        public string $email,
        public string $avatarUrl,
        public ?string $picture = null,
    ) {
    }
}
