<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;
use ReflectionMethod;

final class RequestProcessorExecutor implements RequestProcessorExecutorInterface
{
    public function execute(
        RoutableInterface $requestProcessor,
        array $args,
    ): mixed {
        if (empty($args) === true) {
            return $requestProcessor->process();
        }

        return (new ReflectionMethod($requestProcessor, "process"))->invokeArgs($requestProcessor, $args);
    }
}
