<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * Use to parse an attribute's constructor.
 */
abstract class Constructor
{
    public function __construct(
        private readonly string $qualifiedName,
        protected readonly string $unqualifiedName,
        private readonly string $defaultAttributeClassName,
        protected readonly array $argsForDefault,
        protected readonly mixed $value = null
    ) {
    }

    /**
     * Converts an annotation string to an attribute token.
     *
     * @param string $annotationString The string to convert into a token.
     * @return Token The intermediate representation of a parsed attribute.
     */
    public function toToken(string $annotationString): Token
    {
        $pattern = $this->getPattern();
        $hasAttribute = preg_match($pattern, $annotationString, $matches) === 1;
        $className = $hasAttribute === true ? $this->qualifiedName : $this->defaultAttributeClassName;
        return new Token($className, $this->getArgs($hasAttribute, $matches));
    }

    abstract protected function getArgs(bool $hasAttribute, array $matches): array;

    abstract protected function getPattern(): string;
}
