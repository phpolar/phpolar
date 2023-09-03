<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\DataStorage\CollectionStorageInterface;
use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Core\Rendering\Template;

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
