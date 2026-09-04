<?php

namespace App\Services\Sources\Clients\Cactus\Data;

use App\Data\EntityData;
use App\Services\Sources\Clients\Cactus\Adapters\CactusDataAdapter;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Clients\Cactus\Normalizers\CactusNormalizerFactory;
use App\Services\Sources\Data\Casts\NumberCast;
use App\Services\Sources\Enums\EntityFilter;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class CactusData extends Data
{
    public string $domain = "https://www.cactus.md";

    public function __construct(
        public string $external_id,
        public string $title,

        #[WithCast(NumberCast::class)]
        public float $price,
        #[WithCast(NumberCast::class)]
        public ?float $old_price,

        public ?int $discount,
        public ?string $variant,

        public ?string $image,
        public ?string $out_of_stock,
        public ?string $url,
    ) {
        $this->discount = $discount ?? 0;

        if ($this->url) {
            $this->url = $this->domain . $this->url;
        }

        if ($this->image) {
            $this->image = $this->domain . $this->image;
        }
    }

    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @param CactusSearchParam $searchParam
     * @return EntityData|null
     */
    public function toGeneral(EntityFilter $filter, CactusSearchParam $searchParam): ?EntityData
    {
        $matchId = CactusNormalizerFactory::make($searchParam)->normalize($this);

        if ($matchId === null) {
            return null;
        }

        return CactusDataAdapter::toGeneral($this, $filter, $matchId);
    }
}
