<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Http\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ExceptionHandlerInterface
{
    public function handle(Throwable $e): ResponseInterface| EmptyResponse;
}
