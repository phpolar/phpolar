<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * Use to parse a constructor for a type tag and keyword expression.
 */
final class TypeTag extends Constructor
{
    protected function getPattern(): string
    {
        return "/@var\s+(?P<scalarType>string|null|int|float|double|bool)/s";
    }

    protected function getArgs(bool $hasAttribute, array $matches): array
    {
        return $hasAttribute === true ? [$this->value, $this->getArgOrCast($matches["scalarType"])] : $this->argsForDefault;
    }

    /**
     * @return string|int
     */
    private function getArgOrCast(string $argument)
    {
        return is_numeric($argument) === true ? intval($argument) : $argument;
    }
}
