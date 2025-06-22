<?php

namespace Phpolar\Phpolar\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Builds a PSR-7 Response
 */
interface ResponseBuilderInterface
{
    /**
     * Build a PSR-7 Response
     * using the given string
     * as the response body.
     */
    public function build(string $content = ""): ResponseInterface;
}
