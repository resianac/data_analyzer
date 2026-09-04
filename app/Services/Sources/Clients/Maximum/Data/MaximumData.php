<?php

namespace App\Services\Sources\Clients\Maximum\Data;

use App\Data\EntityData;
use App\Services\Sources\Clients\Maximum\Adapters\MaximumDataAdapter;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Clients\Maximum\Normalizers\MaximumNormalizerFactory;
use App\Services\Sources\Data\Casts\DigitsCast;
use App\Services\Sources\Data\Casts\NumberCast;
use App\Services\Sources\Enums\EntityFilter;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class MaximumData extends Data
{
    public string $domain = "https://maximum.md";

    public function __construct(
        #[WithCast(DigitsCast::class)]
        public string $external_id,
        public string $title,

        #[WithCast(NumberCast::class)]
        public float $price,
        #[WithCast(NumberCast::class)]
        public ?float $old_price,

        #[WithCast(NumberCast::class)]
        public ?int $discount,
        public string $variant,

        public ?string $image,
        public ?string $out_of_stock,
        public ?string $url,
    ) {
        $this->discount = $discount ?? 0;

        if ($this->url) {
            $this->url = $this->domain . $this->url;
        }
    }

    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @param MaximumSearchParam $searchParam
     * @return EntityData|null
     */
    public function toGeneral(EntityFilter $filter, MaximumSearchParam $searchParam): ?EntityData
    {
        $matchId = MaximumNormalizerFactory::make($searchParam)->normalize($this);

        if ($matchId === null) {
            return null;
        }

        return MaximumDataAdapter::toGeneral($this, $filter, $matchId);
    }
}
