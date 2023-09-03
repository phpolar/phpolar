<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;;

use Phpolar\Phpolar\Core\DefaultLabelFormat;
use ReflectionObject;
use ReflectionProperty;

/**
 * Provides support for configuring form field labels.
 */
trait LabelFormatTrait
{
    /**
     * Returns the formatted label.
     *
     * The label is formatted based on the configuration of the property.
     *
     * @api
     */
    public function getLabel(string $name): string
    {
        return $this->getLabelAttr((new ReflectionObject($this))
            ->getProperty($name))
            ->getLabel();
    }

    private function getLabelAttr(ReflectionProperty $prop): Label|DefaultLabelFormat
    {
        $labelAttributes = $prop->getAttributes(Label::class);
        if (count($labelAttributes) > 0) {
            return $labelAttributes[0]->newInstance()->withPropName($prop);
        }
        return new DefaultLabelFormat($prop->getName());
    }
}
