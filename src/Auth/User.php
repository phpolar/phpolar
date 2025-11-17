<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use SensitiveParameter;

/**
 * Represents an authenticated user.
 *
 * @phan-file-suppress PhanWriteOnlyPublicProperty
 */
final readonly class User
{
    public function __construct(
        #[SensitiveParameter]
        public string $name,
        #[SensitiveParameter]
        public string $nickname,
        #[SensitiveParameter]
        public string $email,
        #[SensitiveParameter]
        public string $avatarUrl,
        #[SensitiveParameter]
        public ?string $picture = null,
    ) {}
}
