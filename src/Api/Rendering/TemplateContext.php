<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\Rendering;

/**
 * Represents an object used in a template.
 *
 * The objects variables/properties
 * will be available for use in
 * the template.
 *
 * @example Page.php A template context for a page
 * ```php
 * class Page extends TemplateContext
 * {
 *         public $title = "My Page";
 * }
 * ```
 * @example page-template-1.html
 * ```html
 * <h1><?= $this-title ?></h1>
 * ```
 *
 * @api
 */
abstract class TemplateContext
{
}
