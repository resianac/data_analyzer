<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import catalog from '@/routes/catalog/index.js';
import EntityMasterGrid from '@/components/partials/catalog/list/EntityMasterGrid.vue';
import useNavigation from '@/composables/useNavigation.js';
import Pagination from '@/components/pagination/Pagination.vue';
import EntityFilter from '@/components/partials/catalog/filter/EntityFilter.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    masters: {
        type: Object,
        required: true,
        default: () => {}
    },
    brands: {
        type: Array,
        required: true,
        default: () => []
    }
})

const breadcrumbs = [
    {
        title: 'Catalog',
        href: catalog.index().url,
    },
];

const isLoading = ref(false);

const changePage = (page) => {
    useNavigation('links.index', { page, preserveScroll: false });
};

const changePageSize = (size) => {
    useNavigation('links.index', { pageSize: size, page: 1 });
};

const reloadData = (filters = []) => {
    useNavigation(catalog.index().url, {
        filters,
        page: 1,
        only: ['masters', 'query'],
        onStart: () => isLoading.value = true,
        onFinish: () => isLoading.value = false,
    });
};
</script>

<template>
    <Head><title>Catalog</title></Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <div class="grid grid-cols-1 md:grid-cols-[minmax(250px,300px)_1fr] gap-6">
                <div>
                    <EntityFilter
                        :brands="brands"
                        :loading="isLoading"
                        @update:items="reloadData"
                    />
                </div>

                <!-- Grid -->
                <div class="min-w-0">
                    <EntityMasterGrid :masters="masters.data" />
                </div>
            </div>

            <Pagination
                @page-changed="changePage"
                @page-size-changed="changePageSize"
                :links="masters.links"
                :meta="masters.meta"
                :default-page-size="25"
            />
        </div>
    </AppLayout>
</template>
