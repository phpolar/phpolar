<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Core\Fields\{
    FieldMetadata,
    AutomaticDateField,
    DateField,
    NumberField,
    TextAreaField,
    TextField,
};
use RuntimeException;

abstract class FormControl
{
    protected FieldMetadata $field;

    protected string $errorMessage = "";

    protected bool $hasErrors = true;

    /**
     * @var string
     */
    protected const ERROR_STYLING = "border: 2px solid red";

    private function __construct(FieldMetadata $field)
    {
        $this->field = $field;
        $this->setState();
    }

    /**
     * @api
     * @throws RuntimeException
     */
    public static function create(FieldMetadata $field): FormControl
    {
        switch (true)
        {
            case $field instanceof TextField:
                return new TextFormControl($field);
            case $field instanceof TextAreaField:
                return new TextAreaFormControl($field);
            case $field instanceof NumberField:
                return new NumberFormControl($field);
            // automatic date field has greater precedence than date field
            case $field instanceof AutomaticDateField:
                return new HiddenFormControl($field);
            case $field instanceof DateField:
                return new DateFormControl($field);
            default:
                throw new RuntimeException(get_class($field) . " is not compatible");
        }
    }

    /**
     * @api
     */
    public function getErrorStyling(): string
    {
        return $this->hasErrors === true ? self::ERROR_STYLING : "";
    }

    /**
     * @api
     */
    public function getErrorMesage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @api
     */
    public function getLabel(): string
    {
        return $this->field->label;
    }

    /**
     * @api
     */
    public function getName(): string
    {
        return $this->field->propertyName;
    }

    /**
     * @api
     */
    public function getValue()
    {
        return $this->field->getValue();
    }

    /**
     * @api
     */
    public function isInvalid(): bool
    {
        return $this->hasErrors;
    }

    private function setState(): void
    {
        $errors = array_filter(
            $this->field->validators,
            fn (ValidationInterface $validator) => $validator->isValid() === false
        );
        array_walk(
            $errors,
            function (ValidationInterface $error) {
                $this->errorMessage = $error->getErrorMessage();
            }
        );
        $this->hasErrors = count($errors) > 0;
    }
}
