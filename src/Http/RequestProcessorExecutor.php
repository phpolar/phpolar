<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpRequestProcessor\RequestProcessorExecutorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use ReflectionMethod;

/**
 * Handles execution of a request processor.
 *
 * This implementation uses reflection so that
 * arguments to the `process` method that are
 * decorated with attributes can be passed in
 * after the attributes are invoked.
 */
final class RequestProcessorExecutor implements RequestProcessorExecutorInterface
{
    /**
     * Execute the request processor.
     *
     * Invokes the `process` method with the given arguments.
     */
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
