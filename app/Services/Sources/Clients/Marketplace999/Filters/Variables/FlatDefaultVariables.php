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

                    // REGION
                    [
                        'filterId' => 32,
                        'features' => [
                            ['featureId' => 9, 'optionIds' => [15664, 15668, 15665, 15667]],
                            ['featureId' => 8, 'optionIds' => [13942]],
                        ],
                    ],

                    // APARTMENT SIZE
                    [
                        'filterId' => 30,
                        'features' => [
                            ['featureId' => 241, 'optionIds' => [893, 894, 902]],
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
                            ['featureId' => 1385, 'range' => ['max' => 2110]],
                        ],
                    ],
                    [
                        'filterId' => 9441,
                        'features' => [
                            ['featureId' => 2, 'range' => ['max' => 150000], 'unit' => "UNIT_EUR"],
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
