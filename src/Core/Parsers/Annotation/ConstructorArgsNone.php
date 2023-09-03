<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * Use to parse a constructor with no arguments.
 */
final class ConstructorArgsNone extends Constructor
{
    protected function getPattern(): string
    {
        return "/@(?P<className>{$this->unqualifiedName})\(*\)*/s";
    }

    /**
     * @suppress PhanUnusedProtectedFinalMethodParameter
     */
    protected function getArgs(bool $hasAttribute, array $matches): array
    {
        return [];
    }
}
