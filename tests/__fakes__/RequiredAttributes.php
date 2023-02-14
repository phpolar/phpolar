<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Fakes;

use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Stock\Attributes\DefaultColumn;
use Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength;
use Phpolar\Phpolar\Stock\Attributes\Input;

class RequiredAttributes
{
    public static function get(): array
    {
        return [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
            new Input(InputTypes::Text),
        ];
    }

    public static function getWithoutDateFormat(): array
    {
        return [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultMaxLength(""),
            new Input(InputTypes::Date),
        ];
    }

    public static function getWithoutLabel(): array
    {
        return [
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
            new Input(InputTypes::Text),
        ];
    }

    public static function getWithoutColumn(): array
    {
        return [
            new DefaultLabel(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
            new Input(InputTypes::Text),
        ];
    }

    public static function getWithoutFormControl(): array
    {
        return [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
        ];
    }
    public static function getWithoutMaxLength(): array
    {
        return [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new Input(InputTypes::Text),
        ];
    }
}
