<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

/**
 * Represents an authenticated user.
 *
 * @phan-file-suppress PhanWriteOnlyPublicProperty
 */
final class User
{
    public function __construct(
        public readonly string $name,
        public readonly string $nickname,
        public readonly string $email,
        public readonly string $avatarUrl,
        public readonly ?string $picture = null,
    ) {
    }
}
