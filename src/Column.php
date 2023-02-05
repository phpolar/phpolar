<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Attribute;
use Phpolar\Phpolar\Core\AbstractPropertyNameExtractor;
use Phpolar\Phpolar\Core\ColumnConfig;
use Phpolar\Phpolar\Core\DefaultColumnName;

/**
 * Provides support for configuring the text for column names in records.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column extends AbstractPropertyNameExtractor
{
    public function __construct(private string|ColumnConfig $arg = ColumnConfig::Default)
    {
    }

    /**
     * Returns the configured column name.
     *
     * @api
     */
    public function getColumnName(): string
    {
        return match ($this->arg) {
            ColumnConfig::Default => (new DefaultColumnName($this->propName))->getColumnName(),
            default => (string) $this->arg
        };
    }
}
