<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use DateTime;

/**
 * Holds default configuration values.
 */
final class Defaults
{
    public const DATE_FORMAT = DateTime::ATOM;

    public const FORM_CONTROL_TYPE = "text";

    public const COLUMN_FORMATTER = "ucwords";

    public const LABEL_FORMATTER = "ucwords";

    public const MAX_LENGTH = PHP_INT_MAX;

    public const ERROR_MESSAGE = "Error!";

    public const SUCCESS_MESSAGE = "Success!";

    private function __construct()
    {
    }
}
