<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

/**
 * Contains text for messages.
 */
enum Messages: string
{
    case OversizedValue = "Oversized value entered.";

    case InvalidType = "Invalid input entered.";

    case UknownType = "Value of unknown type entered.";
}
