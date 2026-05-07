<?php

namespace App\Services\Sources\Clients\RabotaMd\Filters\Variables;

class JobVariables
{
    public static function byItems(): array
    {
        return [
            "vacancy_items" => [
                'selector' => ".previewCardContent",
                "fields" => [
                    "external_id" => [
                        "selector" => "div:nth-child(1) a",
                        "attribute" => "name",
                    ],
                    "title" => "div:nth-child(1) a.vacancyShowPopup span",
                    "external_title" => "div:nth-child(1) a span",
                    "url" => [
                        "selector" => "div:nth-child(1) a.vacancyShowPopup",
                        "attribute" => "href"
                    ],
                    "external_url" => [
                        "selector" => "div:nth-child(1) a:nth-child(2)",
                        "attribute" => "href"
                    ],
                    "company" => "div:nth-child(2) a span",
                    "city" => "div:nth-child(2) div:nth-child(2) span",
                    "salary" => "div:nth-child(2) div:nth-child(3) span",
                ],
            ],
            "next_page_button" => ".pagination a.js-ajax-pagination:last-of-type span"
        ];
    }
}
