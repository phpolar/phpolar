<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use DateTime;
use Phpolar\Phpolar\Core\Fields\TextField;

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
