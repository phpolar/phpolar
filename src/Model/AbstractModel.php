<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;;

/**
 * Provides support for configuring the properties
 * of an object for validation, formatting, and storage.
 */
abstract class AbstractModel
{
    use ValidationTrait,
        FieldErrorMessageTrait,
        LabelFormatTrait,
        ColumnNameTrait,
        DataTypeDetectionTrait,
        EntityNameConfigurationTrait,
        SizeConfigurationTrait,
        FormInputTypeDetectionTrait,
        FormControlTypeDetectionTrait;
}
