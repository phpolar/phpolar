<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer\Http;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Based on [this suggestion](https://www.php-fig.org/psr/psr-15/meta/#queue-based-request-handler)
 * this [PSR-15 request handler](https://www.php-fig.org/psr/psr-15/#21-psrhttpserverrequesthandlerinterface)
 * process [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface)
 * that has been added to the queue.
 *
 * Thanks [people](https://www.php-fig.org/psr/psr-15/meta/#7-people)! 👍
 */
interface MiddlewareQueueInterface
{
    /**
     * Adds the provided [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface)
     * to the processing queue.
     */
    public function queue(MiddlewareInterface $middleware): void;
}
