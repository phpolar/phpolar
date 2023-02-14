<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Attributes;

/**
 * Provides a way to configure objects using Attributes.
 *
 * @example Person.php
 */
abstract class Attribute
{
    protected mixed $value;

    /**
     * Makes the Attribute callable.
     *
     * @return mixed|void
     *
     * @api
     */
    abstract public function __invoke();

    public function isAutomaticDateInput(): bool
    {
        return false;
    }

    public function isLabel(): bool
    {
        return false;
    }

    public function isColumn(): bool
    {
        return false;
    }

    public function isFormControl(): bool
    {
        return false;
    }

    public function isDateFormat(): bool
    {
        return false;
    }

    public function isValidator(): bool
    {
        return false;
    }

    public function getFieldClassName(): string
    {
        return "";
    }

    public function withValue(mixed $value): self
    {
        $copy = clone $this;
        $copy->value = $value;
        return $copy;
    }
}
