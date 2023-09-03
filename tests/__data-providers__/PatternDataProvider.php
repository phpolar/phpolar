<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class PatternDataProvider
{
    public const EMAIL_PATTERN = "/^[[:alnum:]]+@[[:alnum:]]+\.[[:alpha:]]+$/";

    public const PHONE_PATTERN = "/^\(?[[:digit:]]{3}\)?[[:space:]]?(-|.)?[[:digit:]]{3}(-|.)?[[:digit:]]{4}$/";

    public function validEmails(): mixed
    {
        return [
            ["test@somewhere.com"],
        ];
    }

    public function validPhoneNumbers(): mixed
    {
        return [
            ["222-222-2222"],
            ["(222)222-2222"],
            ["(222) 222-2222"],
            ["2222222222"],
        ];
    }

    public function invalidEmails(): mixed
    {
        return [
            [null],
            [uniqid()],
            [true],
            [false],
            [""],
            [random_int(PHP_INT_MIN, PHP_INT_MAX)],
        ];
    }
}
