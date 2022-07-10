<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * Use to parse an attribute's constructor.
 */
abstract class Constructor
{
    /**
     * @var string
     */
    private $qualifiedName;

    /**
     * @var string
     */
    protected $unqualifiedName;

    /**
     * @var string
     */
    private $defaultAttributeClassName;

    /**
     * @var array
     */
    protected $argsForDefault;

    /**
     * @var ?mixed
     */
    protected $value;

    public function __construct(
        string $qualifiedName,
        string $unqualifiedName,
        string $defaultAttributeClassName,
        array $argsForDefault,
        $value = null
    ) {
        $this->qualifiedName = $qualifiedName;
        $this->unqualifiedName = $unqualifiedName;
        $this->defaultAttributeClassName = $defaultAttributeClassName;
        $this->argsForDefault = $argsForDefault;
        $this->value = $value;
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
