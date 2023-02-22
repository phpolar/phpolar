<?php

namespace MyApp;

class Page
{
    public string $title;

    public string $backgroundColor = "#fff";

    public string $font = "Arial";

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
