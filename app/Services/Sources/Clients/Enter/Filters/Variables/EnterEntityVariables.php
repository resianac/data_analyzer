<?php

namespace App\Services\Sources\Clients\Enter\Filters\Variables;

use App\Services\Sources\Contracts\Variables\HtmlVariablesInterface;

class EnterEntityVariables implements HtmlVariablesInterface
{
    public static function byItems(): array
    {
        return [
            "entities" => [
                'selector' => ".products-list .product-item",
                "fields" => [
                    "data_gtm" => [
                        'selector' => '.product-item',
                        'attribute' => 'data-gtm',
                        'cast' => 'array'
                    ],
                    "url" => [
                        "selector" => ".product-item .card-body a.stretched-link",
                        "attribute" => "href"
                    ],
                    "image" => [
                        "selector" => ".product-item .card-body .product-img img",
                        "attribute" => "data-src"
                    ],
                    "out_of_stock" => '.product-item.out-of-stock',
                ],
            ],
            "next_page_button" => [
                "selector" => ".products-pagination ul li a.next",
                "attribute" => "aria-label"
            ],
        ];
    }
}
