<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes\Config;

/**
 * Tells the parser that the attribute's constructor
 * uses the name of the property it configures as it's
 * first constructor argument.  The second argument is
 * provided in the annotation or native attribute.
 *
 * For example, to configure max length of a property's value.
 *
 * @example Person.php
 */
final class ConstructorArgsPropertyValueWithSecondArg extends ConstructorArgs
{
}
