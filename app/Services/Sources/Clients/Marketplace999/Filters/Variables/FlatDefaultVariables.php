<?php

namespace App\Services\Sources\Clients\Marketplace999\Filters\Variables;

class FlatDefaultVariables
{
    public static function base(int $limit, int $skip): array
    {
        return [
            'locale' => 'en_US',
            'input' => [
                'subCategoryId' => 1404,
                'source' => 'AD_SOURCE_DESKTOP',
                'pagination' => [
                    'limit' => $limit,
                    'skip' => $skip,
                ],
                'filters' => [
                    [
                        'filterId' => 16,
                        'features' => [
                            ['featureId' => 1, 'optionIds' => [776]],
                        ],
                    ],
                    [
                        'filterId' => 32,
                        'features' => [
                            ['featureId' => 9, 'optionIds' => [15664, 15668, 15665, 15667]],
                        ],
                    ],
                    [
                        'filterId' => 30,
                        'features' => [
                            ['featureId' => 241, 'optionIds' => [893, 894]],
                        ],
                    ],
                    [
                        'filterId' => 2307,
                        'features' => [
                            ['featureId' => 852, 'optionIds' => [19108]],
                        ],
                    ],
                    [
                        'filterId' => 1074,
                        'features' => [
                            ['featureId' => 253, 'optionIds' => [916, 931]],
                        ],
                    ],
                    [
                        'filterId' => 4251,
                        'features' => [
                            ['featureId' => 1385, 'range' => ['max' => 2100]],
                        ],
                    ],
                ]
            ],
            'isWorkCategory' => false,
            'includeCarsFeatures' => false,
            'includeBody' => false,
            'includeOwner' => false,
            'includeBoost' => false,
        ];
    }

}
