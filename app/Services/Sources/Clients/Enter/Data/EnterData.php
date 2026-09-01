<?php

namespace App\Services\Sources\Clients\Enter\Data;

use App\Data\EntityData;
use App\Services\Sources\Clients\Enter\Adapters\EnterDataAdapter;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\Enter\Normalizers\EnterNormalizerFactory;
use App\Services\Sources\Data\Casts\NumberCast;
use App\Services\Sources\Enums\EntityFilter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class EnterData extends Data
{
    public function __construct(
        #[MapInputName('data_gtm.ecommerce.items.0.item_id')]
        public string $external_id,
        #[MapInputName('data_gtm.ecommerce.items.0.item_name')]
        public string $title,
        #[MapInputName('data_gtm.ecommerce.value')]
        public float $price,
        #[MapInputName('data_gtm.ecommerce.currency')]
        public string $currency,
        #[MapInputName('data_gtm.ecommerce.items.0.discount')]
        public float $discount_gtm,
        #[MapInputName('data_gtm.ecommerce.items.0.item_brand')]
        public string $brand,
        #[MapInputName('data_gtm.ecommerce.items.0.item_variant')]
        public string $variant,

        #[WithCast(NumberCast::class)]
        public ?float $discount,
        #[WithCast(NumberCast::class)]
        public ?float $old_price,
        public ?string $out_of_stock,
        public ?string $url,
    ) {
    }

    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @param EnterSearchParam $searchParam
     * @return EntityData
     */
    public function toGeneral(EntityFilter $filter, EnterSearchParam $searchParam): EntityData
    {
        $matchId = EnterNormalizerFactory::make($searchParam)->normalize($this);

        return EnterDataAdapter::toGeneral($this, $filter, $matchId);
    }
}
