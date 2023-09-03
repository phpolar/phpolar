<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api;

use Phpolar\Phpolar\Api\DataStorage\CollectionStorageInterface;
use Phpolar\Phpolar\Api\Rendering\TemplateContext;
use Phpolar\Phpolar\Core\Rendering\Template;

/**
 * Provides common functionality for
 * request handlers.
 */
abstract class RequestHandler
{
    /**
     * Provides support for binding variables
     * to templates.
     */
    protected function getTemplateEngine(): Template
    {
        return new Template();
    }

    /**
     * Provides a way to apply the request handler.
     *
     * @api
     */
    abstract public function __invoke(TemplateContext $page, ?CollectionStorageInterface $storage = null): void;
}
