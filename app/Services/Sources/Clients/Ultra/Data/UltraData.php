<?php

namespace App\Services\Sources\Clients\Ultra\Data;

use App\Data\EntityData;
use App\Services\Sources\Clients\Ultra\Adapters\UltraDataAdapter;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\Normalizers\UltraNormalizerFactory;
use App\Services\Sources\Data\Casts\NumberCast;
use App\Services\Sources\Enums\EntityFilter;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class UltraData extends Data
{
    public function __construct(
        public string $external_id,
        public string $title,

        #[WithCast(NumberCast::class)]
        public float $price,
        #[WithCast(NumberCast::class)]
        public ?float $old_price,

        public ?int $discount,
        public string $variant,

        public ?string $image,
        public ?string $out_of_stock,
        public ?string $url,
    ) {
        $this->discount = $discount ?? 0;
    }

    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @param UltraSearchParam $searchParam
     * @return EntityData|null
     */
    public function toGeneral(EntityFilter $filter, UltraSearchParam $searchParam): ?EntityData
    {
        $matchId = UltraNormalizerFactory::make($searchParam)->normalize($this);

        if ($matchId === null) {
            return null;
        }

        return UltraDataAdapter::toGeneral($this, $filter, $matchId);
    }
}
