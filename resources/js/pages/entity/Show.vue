<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import catalog from '@/routes/catalog/index.js';
import EntityDetail from '@/components/partials/catalog/entity/EntityDetail.vue';
import EntitySourcePriceList from '@/components/partials/catalog/entity/EntitySourcePriceList.vue';
import LineChart from '@/components/charts/LineChart.vue';
import PriceChart from '@/components/partials/catalog/charts/PriceChart.vue';
import DiscountChart from '@/components/partials/catalog/charts/DiscountChart.vue';
import { METRIC } from '@/constants/metrics.js';

const props = defineProps({
    master: {
        type: Object,
        required: true
    },
});

const breadcrumbs = [
    {
        title: 'Catalog',
        href: catalog.index().url,
    },
    {
        title: props.master.title,
        href: catalog.show(props.master.match_id).url,
    },
];
</script>

<template>
    <Head><title>{{ master.title }}</title></Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 py-4 px-4">
            <EntityDetail :master="master" />

            <div class="space-y-6">
                <EntitySourcePriceList
                    :entities="master.entities"
                    :currency="master.entities[0]?.data?.currency || 'MDL'"
                />

                <div class="flex gap-6 justify-between flex-wrap lg:flex-nowrap">
                    <PriceChart
                        class="flex-1"
                        :metrics="master.entities.flatMap(
                            entity => entity.metrics.filter(m => m.key === METRIC.PRICE)
                        )"
                    />
                    <DiscountChart
                        class="flex-1"
                        :metrics="master.entities.flatMap(
                            entity => entity.metrics.filter(m => m.key === METRIC.DISCOUNT)
                        )"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
