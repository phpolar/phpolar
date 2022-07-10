<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Parsers\Annotation;

/**
 * An intermediate representation of a parsed attribute.
 *
 * `annotation string` -> `attibute token` -> `attribute instance`
 */
class Token
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $qualifiedName;

    public function __construct(string $qualifiedName, array $arguments)
    {
        $this->qualifiedName = $qualifiedName;
        $this->arguments = $arguments;
    }

    /**
     * Retrieves the attribute's constructor arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Retrieves the attribute's fully qualified name.
     */
    public function getName(): string
    {
        return $this->qualifiedName;
    }

    /**
     * Creates the final representation the token represents.
     */
    public function newInstance(): object
    {
        $className = $this->qualifiedName;
        return new $className(...$this->getArguments());
    }
}
