<?php

namespace App\Services\Sources\Modules\Catalog\Domain;

enum CatalogSource: string
{
    case ENTER = 'enter';
    case CACTUS = 'cactus';
    case MAXIMUM = 'maximum';
    case ULTRA = 'ultra';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
