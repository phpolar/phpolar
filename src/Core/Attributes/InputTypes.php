<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

enum InputTypes: string
{
    case Text = "text";

    case Textarea = "textarea";

    case Number = "number";

    case Date = "datetime-local";
}
