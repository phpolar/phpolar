<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;
use Efortmeyer\Polar\Api\Model;
use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Api\UIElements\Banner;
use Efortmeyer\Polar\Api\UIElements\DateFormControl;
use Efortmeyer\Polar\Api\UIElements\ErrorBanner;
use Efortmeyer\Polar\Api\UIElements\FormControl;
use Efortmeyer\Polar\Api\UIElements\HiddenFormControl;
use Efortmeyer\Polar\Api\UIElements\SuccessBanner;
use Efortmeyer\Polar\Api\UIElements\TextAreaFormControl;
use Efortmeyer\Polar\Api\UIElements\TextFormControl;

class Form extends TemplateContext
{
    public Banner $banner;

    /**
     * @var FormControl[]
     */
    private array $formControls;

    public function __construct(private Model $model)
    {
        $this->model = $model;
        $this->formControls = array_map([FormControl::class, "create"], $model->getFields());
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function submit(CollectionStorageInterface $storage): void
    {
        if ($this->hasErrors() === true)
        {
            $this->banner = new ErrorBanner();
        } else {
            $this->banner = new SuccessBanner();
            $storage->save(
                $this->getModel()
            );
        }
    }

    /**
     * @return FormControl[]
     */
    public function getTextInputs(): array
    {
        return array_filter(
            $this->formControls,
            fn ($prop) => $prop instanceof TextFormControl
        );
    }

    /**
     * @return FormControl[]
     */
    public function getTextAreaInputs(): array
    {
        return array_filter(
            $this->formControls,
            fn ($prop) => $prop instanceof TextAreaFormControl
        );
    }

    /**
     * @return FormControl[]
     */
    public function getDateInputs(): array
    {
        return array_filter(
            $this->formControls,
            fn ($prop) => $prop instanceof DateFormControl
        );
    }

    /**
     * @return FormControl[]
     */
    public function getHiddenInputs(): array
    {
        return array_filter(
            $this->formControls,
            fn ($prop) => $prop instanceof HiddenFormControl
        );
    }

    public function hasErrors(): bool
    {
        return array_reduce(
            $this->formControls,
            fn (bool $previousError, FormControl $field) => $previousError || $field->isInvalid(),
            false
        );
    }
}