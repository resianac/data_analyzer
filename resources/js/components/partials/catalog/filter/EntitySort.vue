<script setup>
import {usePage} from "@inertiajs/vue3";
import {ArrowDownNarrowWide, ArrowUpNarrowWide, BadgePercent, Layers3, Loader2} from "lucide-vue-next";
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select/index.js";
import {useDebounceFn} from "@vueuse/core";
import {computed, ref, watch} from "vue";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    }
})

const emits = defineEmits(['update:items']);

const { query } = usePage().props;
const selectedSort = ref(query.sort ?? 'source_count');

const SORTS = [
    {
        value: 'source_count',
        label: 'Sources count',
        icon: Layers3,
    },
    {
        value: 'discount_desc',
        label: 'Discount',
        icon: BadgePercent,
    },
    {
        value: 'price_asc',
        label: 'Price (low first)',
        icon: ArrowDownNarrowWide,
    },
    {
        value: 'price_desc',
        label: 'Price (high first)',
        icon: ArrowUpNarrowWide,
    },
];

const selectedSortingObj = computed(() =>
    SORTS.find(i => i.value === selectedSort.value)
);

watch(selectedSort, () => {
    emits('update:items', { sort: selectedSort.value});
})
</script>

<template>
    <Select v-model="selectedSort">
        <SelectTrigger class="w-[240px] bg-background">
            <div
                v-if="selectedSortingObj"
                class="flex items-center gap-2"
            >
                <component
                    :is="selectedSortingObj.icon"
                    class="size-4 text-muted-foreground"
                />

                <span>{{ selectedSortingObj.label }}</span>
            </div>
            <SelectValue v-else placeholder="Sort by" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem
                v-for="sort in SORTS"
                :key="sort.value"
                :value="sort.value"
            >
                <div class="flex items-center gap-2">
                    <component
                        :is="sort.icon"
                        class="size-4 text-muted-foreground"
                    />
                    <span>{{ sort.label }}</span>
                </div>
            </SelectItem>
        </SelectContent>
    </Select>
</template>
