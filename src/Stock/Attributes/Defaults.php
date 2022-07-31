<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use DateTime;
use Efortmeyer\Polar\Core\Fields\TextField;

/**
 * Holds default configuration values.
 */
final class Defaults
{
    public const DATE_FORMAT = DateTime::ATOM;

    public const FORM_CONTROL_TYPE = "text";

    public const FORM_CONTROL_FIELD_CLASS_NAME = TextField::class;

    public const COLUMN_FORMATTER = "ucwords";

    public const LABEL_FORMATTER = "ucwords";

    public const MAX_LENGTH = PHP_INT_MAX;

    private function __construct()
    {
    }
}
