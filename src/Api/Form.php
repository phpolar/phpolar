<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageInterface;
use Phpolar\Phpolar\Api\Model;
use Phpolar\Phpolar\Api\Rendering\TemplateContext;
use Phpolar\Phpolar\Api\UIElements\Banner;
use Phpolar\Phpolar\Api\UIElements\DateFormControl;
use Phpolar\Phpolar\Api\UIElements\ErrorBanner;
use Phpolar\Phpolar\Api\UIElements\FormControl;
use Phpolar\Phpolar\Api\UIElements\HiddenFormControl;
use Phpolar\Phpolar\Api\UIElements\SuccessBanner;
use Phpolar\Phpolar\Api\UIElements\TextAreaFormControl;
use Phpolar\Phpolar\Api\UIElements\TextFormControl;

class Form extends TemplateContext
{
    public Banner $banner;

    /**
     * @var FormControl[]
     */
    private readonly array $formControls;

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
        if ($this->hasErrors() === false) {
            $this->banner = new SuccessBanner();
            $storage->save(
                $this->getModel()
            );
            return;
        }
        $this->banner = new ErrorBanner();
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
