<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Routable\RoutableInterface;

/**
 * Invokes the process method with given arguments.
 */
interface RequestProcessorExecutorInterface
{
    /**
     * @param array<string,mixed> $args
     */
    public function execute(RoutableInterface $requestProcessor, array $args): mixed;
}
