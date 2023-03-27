<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\DefaultColumnName;
use ReflectionObject;
use ReflectionProperty;

/**
 * Provides support for configuring column names of records.
 */
trait ColumnNameTrait
{
    /**
     * Returns the configured column name.
     *
     * @api
     */
    public function getColumnName(string $name): string
    {
        return $this->getColumnAttr((new ReflectionObject($this))
            ->getProperty($name))
            ->getColumnName();
    }

    /**
     * @internal
     */
    private function getColumnAttr(ReflectionProperty $prop): Column|DefaultColumnName
    {
        $labelAttributes = $prop->getAttributes(Column::class);
        if (count($labelAttributes) > 0) {
            return $labelAttributes[0]->newInstance()->withPropName($prop);
        }
        return new DefaultColumnName($prop->getName());
    }
}
