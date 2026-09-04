<script setup>
import { computed, reactive, watch, ref } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Select } from 'vue3-select-component';
import RangeInput from '@/components/forms/range/RangeInput.vue';
import { SOURCES } from '@/constants/sources.js';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';
import { CardHeader } from '@/components/ui/card/index.js';
import { Filter, X, Loader2 } from 'lucide-vue-next';
import { useDebounceFn } from '@vueuse/core';
import SelectWithCount from '@/components/forms/select/SelectWithCount.vue';

const props = defineProps({
    brands: Array,
    loading: {
        type: Boolean,
        default: false,
    }
})

const emits = defineEmits(['update:items']);

const { query } = usePage().props;

const isInternalLoading = ref(props.loading);

const sources = computed(() => {
    return Object.values(SOURCES).map(s => ({
        value: s.id,
        label: s.label
    }))
})

const filters = reactive({
    has_discount: query.has_discount === 'true',
    sources: query.sources?.split(',') ?? [],
    brands: query.brands?.split(',') ?? [],
    price: {
        min: Number(query?.price?.min) || 0,
        max: Number(query?.price?.max) || 500000,
    }
})

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.price.min > 0 || filters.price.max < 500000) count++;
    if (filters.sources.length > 0) count++;
    if (filters.brands.length > 0) count++;
    if (filters.has_discount) count++;
    return count;
})

const clearAllFilters = () => {
    filters.price.min = 0;
    filters.price.max = 500000;
    filters.sources = [];
    filters.brands = [];
    filters.has_discount = false;
}

// const emitWithDelay = () => {
//     isInternalLoading.value = true;
//
//     if (timeoutId) {
//         clearTimeout(timeoutId);
//     }
//
//     const debouncedEmit = useDebounceFn((filters) => {
//         emits('update:items', { ...filters });
//     }, 500);
//
//     timeoutId = setTimeout(() => {
//         emits('update:items', { ...filters });
//         timeoutId = null;
//     }, 1000);
// }

watch(() => props.loading, (newVal) => {
    if (!newVal && isInternalLoading.value) {
        isInternalLoading.value = false;
    }
})

const debouncedEmit = useDebounceFn((filtersData) => {
    emits('update:items', { ...filtersData });
}, 1000);

watch(filters, () => {
    isInternalLoading.value = true;
    debouncedEmit(filters);
}, { deep: true })

</script>

<template>
    <Card>
        <CardHeader class="px-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Filter class="h-4 w-4 text-muted-foreground" />
                    <span class="text-sm font-medium text-foreground">Filters</span>
                    <span
                        v-if="activeFiltersCount > 0"
                        class="inline-flex items-center justify-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                    >
                        {{ activeFiltersCount }}
                    </span>
                    <Loader2
                        v-if="isInternalLoading"
                        class="h-4 w-4 animate-spin text-primary"
                    />
                </div>
                <button
                    v-if="activeFiltersCount > 0"
                    @click="clearAllFilters"
                    aria-label="Clear all filters"
                    class="cursor-pointer flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground transition-colors"
                >
                    <X class="h-3 w-3" />
                    Clear all
                </button>
            </div>
        </CardHeader>

        <CardContent class="px-4">
            <div class="flex flex-col gap-3">
                <div class="flex-1">
                    <Label class="mb-1.5 text-xs text-muted-foreground">Price</Label>
                    <RangeInput
                        v-model="filters.price"
                        :min="0"
                        :max="500000"
                        :step="500"
                    />
                </div>

                <div class="flex-1">
                    <Label class="mb-1.5 text-xs text-muted-foreground">Brand</Label>
                    <SelectWithCount
                        v-model="filters.brands"
                        :options="brands"
                        multiple
                        clearable
                        placeholder="All brands"
                    />

<!--                    <Select-->
<!--                        v-model="filters.brands"-->
<!--                        :options="brands"-->
<!--                        placeholder="All brands"-->
<!--                        multiple-->
<!--                        clearable-->
<!--                    />-->
                </div>

                <div class="flex-1">
                    <Label class="mb-1.5 text-xs text-muted-foreground">Source</Label>
                    <Select
                        v-model="filters.sources"
                        :options="sources"
                        placeholder="All sources"
                        multiple
                        clearable
                        class="vue-select"
                    />
                </div>

                <div class="flex w-full items-center justify-between">
                    <Label class="text-xs text-muted-foreground">On sale</Label>
                    <Switch
                        :modelValue="filters.has_discount"
                        @update:modelValue="filters.has_discount = $event"
                        label="On sale"
                    />
                </div>
            </div>

            <div v-if="activeFiltersCount > 0" class="mt-4 flex flex-wrap gap-1.5 pt-4 border-t border-border/50">
                <span
                    v-if="filters.price.min > 0 || filters.price.max < 500000"
                    class="inline-flex items-center gap-1 rounded-full bg-muted/80 px-2.5 py-0.5 text-xs"
                >
                    {{ filters.price.min > 0 ? 'From ' + filters.price.min : '' }}
                    {{ filters.price.min > 0 && filters.price.max < 500000 ? '—' : '' }}
                    {{ filters.price.max < 500000 ? 'To ' + filters.price.max : '' }}
                    <X
                        class="h-3 w-3 cursor-pointer hover:text-foreground"
                        @click="filters.price = { min: 0, max: 500000 }"
                    />
                </span>
                <span
                    v-for="source in filters.sources"
                    :key="source"
                    class="inline-flex items-center gap-1 rounded-full bg-muted/80 px-2.5 py-0.5 text-xs"
                >
                    {{ sources.find(s => s.value === source)?.label || source }}
                    <X
                        class="h-3 w-3 cursor-pointer hover:text-foreground"
                        @click="filters.sources = filters.sources.filter(s => s !== source)"
                    />
                </span>
                <span
                    v-for="brand in filters.brands"
                    :key="brand"
                    class="inline-flex items-center gap-1 rounded-full bg-muted/80 px-2.5 py-0.5 text-xs"
                >
                    {{ brand }}
                    <X
                        class="h-3 w-3 cursor-pointer hover:text-foreground"
                        @click="filters.brands = filters.brands.filter(b => b !== brand)"
                    />
                </span>
                <span
                    v-if="filters.has_discount"
                    class="inline-flex items-center gap-1 rounded-full bg-muted/80 px-2.5 py-0.5 text-xs"
                >
                    On sale
                    <X
                        class="h-3 w-3 cursor-pointer hover:text-foreground"
                        @click="filters.has_discount = false"
                    />
                </span>
            </div>
        </CardContent>
    </Card>
</template>
