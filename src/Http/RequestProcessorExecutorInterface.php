<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorInterface;

/**
 * Invokes the process method with given arguments.
 */
interface RequestProcessorExecutorInterface
{
    /**
     * @param array<string,mixed> $args
     */
    public function execute(RequestProcessorInterface $requestProcessor, array $args): mixed;
}
