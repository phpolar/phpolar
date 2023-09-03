<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\Label;

final class LabelDataProvider
{
    public function getUnconfiguredPropertyTestCases(): array
    {
        return [
            [
                "Prop",
                "prop",
                new class() extends AbstractModel
                {
                    public string $prop;
                }
            ]
        ];
    }

    public function getLabelTestCases(): array
    {
        return [
            [
                "Prop",
                "prop",
                new class() extends AbstractModel
                {
                    #[Label]
                    public string $prop;
                }
            ],
            [
                "AnotherProp",
                "anotherProp",
                new class() extends AbstractModel
                {
                    #[Label]
                    public string $anotherProp;
                }
            ],
            [
                "AndAgainAnotherProp",
                "andAgainAnotherProp",
                new class() extends AbstractModel
                {
                    #[Label]
                    public string $andAgainAnotherProp;
                }
            ],
        ];
    }


    public function getConfiguredLabelTestCases(): array
    {
        return [
            [
                "sOmethingELSE",
                "prop",
                new class() extends AbstractModel
                {
                    #[Label("sOmethingELSE")]
                    public string $prop;
                }
            ],
        ];
    }
}
