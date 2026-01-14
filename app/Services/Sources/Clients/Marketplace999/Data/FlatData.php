<?php

namespace App\Services\Sources\Clients\Marketplace999\Data;

use App\Services\Sources\Clients\Marketplace999\Adapters\FlatDataAdapter;
use App\Services\Sources\Clients\Marketplace999\Cast\MarketplaceDateCast;
use App\Services\Sources\Configs\Marketplace999Config;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

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
        #[MapInputName('owner.login')]
        public ?string $owner,

        #[WithCast(MarketplaceDateCast::class)]
        public Carbon|string|null $posted,

        #[WithCast(MarketplaceDateCast::class)]
        public Carbon|string|null $reseted,
        public ?string $url = null,
    ) {
        $this->url = Marketplace999Config::$baseUrl . $this->external_id;
    }


    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @return EntityData
     */
    public function toGeneral(EntityFilter $filter): EntityData
    {
        return FlatDataAdapter::toGeneral($this, $filter);
    }
}
