<?php

namespace App\Services\Sources\Clients\Bomba\Filters\Variables;

use App\Services\Sources\Contracts\Variables\HtmlVariablesInterface;

class BombaEntityVariables implements HtmlVariablesInterface
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
                        "selector" => ".product-item .product-img img",
                        "attribute" => "data-src"
                    ],
                    "old_price" => ".product-item .price-old-with-discount div:nth-child(1)",
                    "discount" => ".product-item .price-old-with-discount div:nth-child(2) div:nth-child(1)",
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
