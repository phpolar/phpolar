<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

class LabelTestData
{
    public static function text()
    {
        return [
            [uniqid()],
            [uniqid()]
        ];
    }
}
