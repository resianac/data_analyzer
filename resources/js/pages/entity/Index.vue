<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import catalog from '@/routes/catalog/index.js';
import EntityMasterGrid from '@/components/partials/catalog/list/EntityMasterGrid.vue';

const props = defineProps({
    masters: {
        type: Object,
        required: true,
        default: () => []
    }
})

console.log(props.masters);

const breadcrumbs = [
    {
        title: 'Catalog',
        href: catalog.index().url,
    },
];
</script>

<template>
    <Head><title>Catalog</title></Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <EntityMasterGrid :masters="masters.data" />
        </div>
    </AppLayout>
</template>
