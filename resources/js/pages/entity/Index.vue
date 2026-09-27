<script setup>
import Pagination from '@/components/pagination/Pagination.vue';
import EntityFilter from '@/components/partials/catalog/filter/EntityFilter.vue';
import EntityMasterGrid from '@/components/partials/catalog/list/EntityMasterGrid.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import useNavigation from '@/composables/useNavigation.js';
import AppLayout from '@/layouts/AppLayout.vue';
import catalog from '@/routes/catalog/index.js';
import { Head, usePage } from '@inertiajs/vue3';
import {
    ArrowDownNarrowWide,
    ArrowUpNarrowWide,
    BadgePercent,
    Layers3,
    Loader2,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import EntitySort from "@/components/partials/catalog/filter/EntitySort.vue";

defineProps({
    masters: {
        type: Object,
        required: true,
        default: () => {},
    },
    brands: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const breadcrumbs = [
    {
        title: 'Catalog',
        href: catalog.index().url,
    },
];

const isLoading = ref(false);
const page = usePage();

const changePage = (page) => {
    useNavigation(catalog.index().url, { page, preserveScroll: false });
};

const changePageSize = (size) => {
    useNavigation(catalog.index().url, { pageSize: size, page: 1 });
};

const reloadData = (filters = []) => {
    useNavigation(catalog.index().url, {
        filters,
        page: 1,
        only: ['masters', 'query'],
        onStart: () => (isLoading.value = true),
        onFinish: () => (isLoading.value = false),
    });
};
</script>

<template>
    <Head><title>Catalog</title></Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-[minmax(250px,300px)_1fr]">
                <div>
                    <EntityFilter
                        :brands="brands"
                        :loading="isLoading"
                        @update:items="reloadData"
                    />
                </div>

                <!-- Grid -->
                <div class="min-w-0">
                    <div class="mb-4 flex items-center justify-end gap-2">
                        <EntitySort
                            :loading="isLoading"
                            @update:items="reloadData"
                        />
                    </div>

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
