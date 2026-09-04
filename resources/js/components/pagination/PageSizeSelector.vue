<script setup>
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    defaultPageSize: {
        type: Number,
        default: 100,
    }
})

const emit = defineEmits(['page-size-changed']);
const route = usePage()

const pageSizes = [10, 25, 50, 100];

const { query } = usePage();
const queryParams = computed(() => query || {});

const pageSize = ref(Number(queryParams.value.pageSize) || props.defaultPageSize);

const handlePageSizeChange = () => {
    emit('page-size-changed', pageSize.value);
};
</script>

<template>
    <div class="w-16">
        <select
            id="pageSize"
            v-model="pageSize"
            @change="handlePageSizeChange"
            class="
                bg-theme-surface
                border border-theme-select-border
                text-theme-primary
                text-sm rounded-lg
                focus:ring-theme-focus
                focus:border-theme-focus
                block w-full p-2.5
            "
        >
            <option
                v-for="size in pageSizes"
                :key="size"
                :value="size"
                :selected="pageSize === size"
            >
                {{ size }}
            </option>
        </select>
    </div>
</template>
