<?php

namespace App\Services\Sources\Clients\Maximum\Filters\Variables;

use App\Services\Sources\Contracts\Variables\HtmlVariablesInterface;

class MaximumEntityVariables implements HtmlVariablesInterface
{
    public static function byItems(): array
    {
        return [
            "entities" => [
                'selector' => ".product__item",
                "fields" => [
                    "external_id" => ".product-item-description-code",
                    'title' => ".product__item__title a",
                    'variant' => ".product-item-description",
                    'old_price' => ".product__item__price__block .product__item__price-old",
                    'price' => ".product__item__price__block .product__item__price-current",
                    'discount' => ".product__item__price__block .product__item__sale-stats .product__item__sale-field span b",
                    "url" => [
                        "selector" => ".product__item__image a",
                        "attribute" => "href"
                    ],
                    "image" => [
                        "selector" => ".product__item__image a img",
                        "attributes" => [
                            "data-src",
                            "src",
                        ],
                    ],
                    "out_of_stock" => '[class="text_price_flex "]',
                ],
            ],
            "next_page_button" => ".paginator-pages-list li a span.fa-chevron-right",
        ];
    }
}
