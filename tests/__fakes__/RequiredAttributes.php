<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Fakes;

use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength;
use Efortmeyer\Polar\Stock\Attributes\Input;

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
