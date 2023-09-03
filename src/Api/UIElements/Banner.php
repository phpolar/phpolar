<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

abstract class Banner
{
    protected const BACKGROUND_COLOR = "#fff";

    protected const PADDING = "20px 100px";

    protected const WIDTH = "auto";

    protected const HEIGHT = "auto";

    protected const ALIGNMENT = "center";

    protected const LINE_HEIGHT = self::HEIGHT;

    protected const DISPLAY = "inline-block";

    /**
     * @api
     */
    public function getStyle(): string
    {
        $rules = [
            "background-color: " . static::BACKGROUND_COLOR,
            "width: " . static::WIDTH,
            "height: " . static::HEIGHT,
            "line-height: " . static::LINE_HEIGHT,
            "text-align: " . static::ALIGNMENT,
            "display: " . static::DISPLAY,
            "padding: " . static::PADDING,
        ];
        return implode(";", $rules);
    }

    /**
     * @api
     */
    abstract public function getMessage(): string;
}
