<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

/**
 * Contains a set of values representing scalar types.
 */
enum ScalarTypes: string
{
    case String = "string";

    case Integer = "int";

    case Float = "float";

    case Double = "double";

    case Null = "null";

    case Bool = "bool";
}
