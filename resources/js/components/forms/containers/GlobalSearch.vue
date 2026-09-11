<script setup>
import SearchInput from '@/components/forms/input/SearchInput.vue';
import EntityMasterRow from '@/components/partials/catalog/card/EntityMasterRow.vue';
import { ref, watch } from 'vue';
import useAjax from '@/composables/useAjax.js';
import catalog from '@/routes/catalog/index.js';
import { LoaderCircle, Search } from 'lucide-vue-next';
import { useDebounceFn } from '@vueuse/core';

const query = ref('')
const masters = ref([])
const { state, get } = useAjax()

watch(query, (value) => {
    state.loading = true;

    debouncedFetchMasters(value)
})

const fetchMasters = async (search) => {
    await get(catalog.index().url, {search, pageSize: 15})

    masters.value = state.data.masters.data;
}

const debouncedFetchMasters = useDebounceFn((value) => {
    if (!value.trim()) {
        masters.value = []
        state.loading = false;
        return
    }

    fetchMasters(value)
}, 1000)
</script>

<template>
    <SearchInput
        v-model="query"
        :loading="state.loading"
    >
        <template #content>
            <div class="flex items-center justify-between px-2 py-1.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-foreground">
                        Search results
                    </span>

                    <span v-if="masters.length && !state.loading" class="flex items-center justify-between px-2">
                        <span class="text-[10px] text-muted-foreground">
                            Showing {{ masters.length }} results of {{ state.data?.masters.meta.total }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="overflow-y-auto rounded-lg border border-border/40 max-h-[70vh]">
                <EntityMasterRow
                    v-for="master in masters"
                    :key="master.id"
                    :master="master"
                    compact
                />

                <div
                    v-if="state.loading"
                    class="flex gap-1 items-center justify-center py-8 text-sm text-muted-foreground"
                >
                    <LoaderCircle v-if="state.loading" class="size-4 animate-spin text-muted-foreground" />
                    Searching...
                </div>

                <div
                    v-else-if="!masters.length"
                    class="flex flex-col items-center justify-center gap-1 py-8"
                >
                    <Search class="size-5 text-muted-foreground/50" />

                    <span class="text-sm font-medium">Nothing found</span>
                    <span class="text-xs text-muted-foreground">Try another search query</span>
                </div>
            </div>
        </template>
    </SearchInput>
</template>
