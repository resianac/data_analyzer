<?php

namespace App\Services\Sources\Clients\Ultra\Filters\Variables;

use App\Services\Sources\Contracts\Variables\HtmlVariablesInterface;

class UltraEntityVariables implements HtmlVariablesInterface
{
    public static function byItems(): array
    {
        return [
            "entities" => [
                'selector' => "#products .product-card",
                "fields" => [
                    "external_id" => [
                        'selector' => '.product-card',
                        'attribute' => 'data-code',
                    ],
                    'title' => ".product-card a.product-card__link .product-card__title",
                    'variant' => ".product-card a.product-card__link .product-card__specs-title",
                    'old_price' => ".product-card .product-card__discount-block .product-card__old-price",
                    'discount' => ".product-card .product-card__discount-block .product-card__discount-percent-wr span",
                    'price' => ".product-card .product-card__current-price",
                    "url" => [
                        "selector" => ".product-card a.product-card__link",
                        "attribute" => "href"
                    ],
                    "image" => [
                        "selector" => ".product-card a.product-card__link img",
                        "attribute" => "src"
                    ],
                    "out_of_stock" => '.product-card .product-card__out-of-stock',
                ],
            ],
            "next_page_button" => ".pagination__actions a:last-child span.pagination__raquo",
        ];
    }
}
