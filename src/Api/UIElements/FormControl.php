<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Core\Fields\{
    FieldMetadata,
    AutomaticDateField,
    DateField,
    NumberField,
    TextAreaField,
    TextField,
};
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class FormControl
{
    protected string $errorMessage = "";

    protected bool $hasErrors = true;

    /**
     * @var string
     */
    protected const ERROR_STYLING = "border: 2px solid red";

    private function __construct(protected FieldMetadata $field)
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
        return match (true) {
            $field instanceof TextField => new TextFormControl($field),
            $field instanceof TextAreaField => new TextAreaFormControl($field),
            $field instanceof NumberField => new NumberFormControl($field),
            $field instanceof AutomaticDateField => new HiddenFormControl($field),
            $field instanceof DateField => new DateFormControl($field),
            default => throw new RuntimeException(get_class($field) . " is not compatible")
        };
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
