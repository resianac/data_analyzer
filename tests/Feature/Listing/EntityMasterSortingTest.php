<?php

use App\Models\Entity;
use App\Models\EntityMaster;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;

function createMasterWithOffers(string $matchId, array $offers): EntityMaster
{
    $master = EntityMaster::query()->create([
        'match_id' => $matchId,
        'title' => $matchId,
        'category' => 'tv',
    ]);

    foreach ($offers as $index => $offer) {
        Entity::query()->create([
            'entity_master_id' => $master->id,
            'external_id' => "{$matchId}-{$index}",
            'title' => $matchId,
            'source' => $offer['source'] ?? SourceClientType::cases()[$index % count(SourceClientType::cases())],
            'filter_type' => EntityFilter::ENTER_ENTITY,
            'data' => [
                'price' => $offer['price'],
                'discount' => $offer['discount'],
                'is_out_of_stock' => $offer['is_out_of_stock'] ?? false,
            ],
        ]);
    }

    return $master;
}

it('sorts catalog masters by sources discounts and best price', function () {
    $sortedMasterIds = fn (string $sort): array => $this
        ->getJson(route('catalog.index', ['sort' => $sort, 'pageSize' => 50]))
        ->assertSuccessful()
        ->json('masters.data.*.match_id');

    createMasterWithOffers('single-source', [
        ['price' => 300, 'discount' => 10],
    ]);

    createMasterWithOffers('three-sources', [
        ['price' => 500, 'discount' => 5],
        ['price' => 450, 'discount' => 15],
        ['price' => 400, 'discount' => 20],
    ]);

    createMasterWithOffers('largest-discount', [
        ['price' => 700, 'discount' => 60],
        ['price' => 650, 'discount' => 25],
    ]);

    expect($sortedMasterIds('source_count'))->toBe([
        'three-sources',
        'largest-discount',
        'single-source',
    ])->and($sortedMasterIds('discount_desc'))->toBe([
        'largest-discount',
        'three-sources',
        'single-source',
    ])->and($sortedMasterIds('price_asc'))->toBe([
        'single-source',
        'three-sources',
        'largest-discount',
    ])->and($sortedMasterIds('price_desc'))->toBe([
        'largest-discount',
        'three-sources',
        'single-source',
    ]);
});

it('falls back to source count for an unknown sort value', function () {
    $sortedMasterIds = fn (string $sort): array => $this
        ->getJson(route('catalog.index', ['sort' => $sort, 'pageSize' => 50]))
        ->assertSuccessful()
        ->json('masters.data.*.match_id');

    createMasterWithOffers('one', [
        ['price' => 100, 'discount' => 0],
    ]);
    createMasterWithOffers('two', [
        ['price' => 200, 'discount' => 0],
        ['price' => 190, 'discount' => 0],
    ]);

    expect($sortedMasterIds('unsupported'))->toBe(['two', 'one']);
});

it('counts distinct sources instead of offers', function () {
    createMasterWithOffers('repeated-source', [
        ['price' => 100, 'discount' => 0, 'source' => SourceClientType::ENTER],
        ['price' => 110, 'discount' => 0, 'source' => SourceClientType::ENTER],
        ['price' => 120, 'discount' => 0, 'source' => SourceClientType::ENTER],
    ]);
    createMasterWithOffers('two-sources', [
        ['price' => 200, 'discount' => 0, 'source' => SourceClientType::ENTER],
        ['price' => 210, 'discount' => 0, 'source' => SourceClientType::CACTUS],
    ]);

    $ids = $this
        ->getJson(route('catalog.index', ['sort' => 'source_count', 'pageSize' => 50]))
        ->assertSuccessful()
        ->json('masters.data.*.match_id');

    expect($ids)->toBe(['two-sources', 'repeated-source']);
});

it('places unavailable masters after available ones for every sort', function () {
    createMasterWithOffers('available', [
        ['price' => 100, 'discount' => 1],
    ]);
    createMasterWithOffers('available-in-one-source', [
        ['price' => 150, 'discount' => 2, 'is_out_of_stock' => true],
        ['price' => 140, 'discount' => 3, 'is_out_of_stock' => false],
    ]);
    createMasterWithOffers('unavailable-cheap', [
        ['price' => 1, 'discount' => 99, 'is_out_of_stock' => true],
        ['price' => 2, 'discount' => 98, 'is_out_of_stock' => true],
        ['price' => 3, 'discount' => 97, 'is_out_of_stock' => true],
    ]);
    createMasterWithOffers('unavailable-expensive', [
        ['price' => 10_000, 'discount' => 100, 'is_out_of_stock' => true],
    ]);

    foreach (['source_count', 'discount_desc', 'price_asc', 'price_desc'] as $sort) {
        $ids = $this
            ->getJson(route('catalog.index', ['sort' => $sort, 'pageSize' => 50]))
            ->assertSuccessful()
            ->json('masters.data.*.match_id');

        expect(array_slice($ids, 0, 2))->each->toBeIn([
            'available',
            'available-in-one-source',
        ])->and(array_slice($ids, 2))->each->toBeIn([
            'unavailable-cheap',
            'unavailable-expensive',
        ]);
    }
});
