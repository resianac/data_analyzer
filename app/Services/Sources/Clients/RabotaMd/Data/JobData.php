<?php

namespace App\Services\Sources\Clients\RabotaMd\Data;

use App\Services\Sources\Clients\Marketplace999\Adapters\FlatDataAdapter;
use App\Services\Sources\Clients\Marketplace999\Cast\MarketplaceDateCast;
use App\Services\Sources\Clients\RabotaMd\Adapters\JobDataAdapter;
use App\Services\Sources\Clients\RabotaMd\RabotaMdClient;
use App\Services\Sources\Configs\Marketplace999Config;
use App\Services\Sources\Configs\RabotaMdConfig;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class JobData extends Data
{
    public function __construct(
        public ?string $external_id,
        public ?string $external_title,
        public ?string $title,
        public ?string $company,
        public ?string $city,
        public ?string $salary,

        public ?string $external_url,
        public ?string $url,
    ) {
        if ($this->url) {
            $this->url = RabotaMdConfig::$baseUrl . ltrim($this->url, '/ru');
            $this->external_url = null;
        }
        if ($this->external_id) {
            $this->external_id = Str::of($this->external_id)->afterLast('_')->value();
        }
        if ($this->title) {
            $this->external_title = null;
        }

        if ($this->external_title) {
            $this->title = $this->external_title;
        }
        if ($this->external_url) {
            $this->url = $this->external_url;
        }
    }


    /**
     * Transform data to general DTO
     *
     * @param EntityFilter $filter
     * @return EntityData
     */
    public function toGeneral(EntityFilter $filter): EntityData
    {
        return JobDataAdapter::toGeneral($this, $filter);
    }
}
