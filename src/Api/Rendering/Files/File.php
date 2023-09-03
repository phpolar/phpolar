<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Rendering\Files;

abstract class File
{
    /**
     * @var string
     */
    private $pathToFile;

    public function __construct(string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
    }

    abstract protected function getContentType(): string;

    /**
     * Displays the file.
     *
     * @api
     */
    public function render(): void
    {
        $this->printContentType();
        ob_start();
        include $this->pathToFile;
        ob_end_flush();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function printContentType(): void
    {
        if (headers_sent() === false) {
            header("Content-Type: {$this->getContentType()}");
        }
    }
}
