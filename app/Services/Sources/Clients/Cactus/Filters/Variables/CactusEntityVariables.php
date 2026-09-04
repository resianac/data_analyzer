<?php

namespace App\Services\Sources\Clients\Cactus\Filters\Variables;

use App\Services\Sources\Contracts\Variables\HtmlVariablesInterface;

class CactusEntityVariables implements HtmlVariablesInterface
{
    public static function byItems(): array
    {
        return [
            "entities" => [
                'selector' => ".catalog__pill",
                "fields" => [
                    "external_id" => [
                        'selector' => '.catalog__pill',
                        'attribute' => 'data-product',
                    ],
                    'title' => "span.catalog__pill__text__title",
                    'variant' => null,
                    'old_price' => ".catalog__pill__controls__price_old",
                    'discount' => null,
                    'price' => '[class="catalog__pill__controls__price"]',
                    "url" => [
                        "selector" => "a",
                        "attribute" => "href"
                    ],
                    "image" => [
                        "selector" => ".catalog__pill__img__prime",
                        "attribute" => "src",
                    ],
                    "out_of_stock" => null,
                ],
            ],
            "next_page_button" => "#pnlPaging nav.ajax-pager button",
        ];
    }
}
