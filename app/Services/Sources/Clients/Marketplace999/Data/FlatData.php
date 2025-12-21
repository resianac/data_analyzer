<?php

namespace App\Services\Sources\Clients\Marketplace999\Data;

use App\Services\Sources\Clients\Marketplace999\Adapters\FlatDataAdapter;
use App\Services\Sources\Clients\Marketplace999\Cast\MarketplaceDateCast;
use App\Services\Sources\Configs\Marketplace999Config;
use App\Services\Sources\Data\EntityData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Carbon\Carbon;

class FlatData extends Data
{
    public function __construct(
        #[MapInputName('id')]
        public ?string $external_id,
        public ?string $title,

        #[MapInputName('price.value.value')]
        public ?int $price,
        #[MapInputName('oldPrice.value')]
        public ?int $oldPrice,

        #[MapInputName('pricePerMeter.value')]
        public ?string $pricePerMeter,

        #[MapInputName('rooms.value.translated')]
        public ?string $rooms,
        #[MapInputName('floor.value.translated')]
        public ?string $floor,
        #[MapInputName('totalFloors.value.translated')]
        public ?string $totalFloors,

        #[WithCast(MarketplaceDateCast::class)]
        public Carbon|string|null $reseted,
        public ?string $url = null,
    ) {
        $this->url = Marketplace999Config::$baseUrl . $this->external_id;
    }


    /**
     * Transform data to general DTO
     *
     * @return EntityData
     */
    public function toGeneral(): EntityData
    {
        return FlatDataAdapter::toGeneral($this);
    }
}
