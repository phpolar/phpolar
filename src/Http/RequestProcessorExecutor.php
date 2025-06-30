<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorExecutorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use ReflectionMethod;

final class RequestProcessorExecutor implements RequestProcessorExecutorInterface
{
    public function execute(
        RequestProcessorInterface $requestProcessor,
        array $args,
    ): mixed {
        if (empty($args) === true) {
            return $requestProcessor->process();
        }

        return (new ReflectionMethod($requestProcessor, "process"))->invokeArgs($requestProcessor, $args);
    }
}
