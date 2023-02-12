<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

;

/**
 * Provides support for configuring the properties
 * of an object for validation, formatting, and storage.
 */
abstract class AbstractModel
{
    use ValidationTrait;
    use FieldErrorMessageTrait;
    use LabelFormatTrait;
    use ColumnNameTrait;
    use DataTypeDetectionTrait;
    use EntityNameConfigurationTrait;
    use SizeConfigurationTrait;
    use FormInputTypeDetectionTrait;
    use FormControlTypeDetectionTrait;
}
