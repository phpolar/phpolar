<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * Use to parse a constructor with one argument.
 */
final class ConstructorArgsOne extends Constructor
{
    protected function getPattern(): string
    {
        return "/@{$this->unqualifiedName}\(['\"]*(?P<argument>[^\n'\"]+)['\"]*\)/s";
    }

    protected function getArgs(bool $hasAttribute, array $matches): array
    {
        return $hasAttribute === true ? [$this->getArgOrCast($matches["argument"])] : $this->argsForDefault;
    }

    /**
     * @return string|int
     */
    private function getArgOrCast(string $argument)
    {
        return is_numeric($argument) === true ? intval($argument) : $argument;
    }
}
