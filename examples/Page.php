<?php

class Page
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $backgroundColor = "#fff";

    /**
     * @var string
     */
    public $font = "Arial";

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
