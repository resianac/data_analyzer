<script setup>
import { Box, ChevronRight, Star, Flame } from 'lucide-vue-next';
import { useSource } from '@/composables/useSource.js';
import { Link } from '@inertiajs/vue3';
import EntitySourceRow from '@/components/partials/catalog/entity/EntitySourceRow.vue';
import catalog from '@/routes/catalog/index.js';
import { computed } from 'vue';

const props = defineProps({
    master: {
        type: Object,
        required: true
    }
});

const entities = props.master.entities || [];
const currency = entities[0]?.data?.currency || 'MDL';

const masterImage = computed(() => entities.find(i => i.data.image)?.data.image ?? null);

const sortedEntities = [...entities].sort((a, b) => {
    const labelA = useSource(a.source).label.value || a.source;
    const labelB = useSource(b.source).label.value || b.source;
    return labelA.localeCompare(labelB);
});

const bestPrice = sortedEntities.length > 0
    ? Math.min(...sortedEntities.filter(e => !e.data.is_out_of_stock).map(e => e.data?.price || Infinity))
    : null;

const formattedPrice = computed(() => {
    if (!bestPrice) return null;
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: currency || 'MDL',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(bestPrice);
});

const hasDiscount = computed(() => {
    return sortedEntities.some(e => e.data?.old_price && e.data?.price < e.data?.old_price);
});
</script>

<template>
    <div class="flex hover:bg-accent/40 transition-colors border-b border-border/40 last:border-b-0 px-2 py-2">
        <div class="flex-shrink-0 w-24 h-24 bg-muted/20 rounded-lg overflow-hidden flex items-center justify-center border border-border/20">
            <img
                v-if="masterImage"
                :src="masterImage"
                :alt="master.title || 'Product'"
                class="w-full h-full object-cover"
            />
            <Box v-else class="size-5 text-muted-foreground" />
        </div>

        <div class="group flex flex-1 min-w-0  flex-col gap-3 px-3 py-2">
            <div class="min-w-0">
                <Link
                    :href="catalog.show(master.match_id)"
                    class="hover:text-primary transition-colors"
                    :title="master.title || 'Untitled'"
                >
                    <div class="text-sm font-medium truncate">
                        {{ master.title || 'Untitled' }}
                    </div>
                </Link>

                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span v-if="master.category" class="text-[10px] text-muted-foreground bg-muted/30 px-1.5 py-0.5 rounded-full">
                        {{ master.category }}
                    </span>
                    <span class="text-xs text-muted-foreground">
                        {{ entities.length }} {{ entities.length === 1 ? 'source' : 'sources' }}
                    </span>
                    <span v-if="hasDiscount" class="flex items-center gap-1 text-[11px] text-green-600 dark:text-green-400 font-medium">
                        <Flame class="size-4" /> Discount
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-1 flex-shrink-0 overflow-x-auto hide-scrollbar">
                <EntitySourceRow
                    v-for="(entity, index) in sortedEntities"
                    :key="entity.id || index"
                    :entity="entity"
                    :is-best-price="entity.data?.price === bestPrice && sortedEntities.length > 1"
                    :currency="currency"
                    compact
                />
            </div>
        </div>

        <div class="flex items-center gap-3 flex-shrink-0">
            <div
                v-if="sortedEntities.length > 1"
                class="flex items-center gap-1.5 rounded-lg border border-primary/20 bg-primary/10 px-2 py-1 shadow-sm"
            >
                <div class="flex flex-col items-end leading-none">
                    <span class="text-[9px] font-medium uppercase tracking-wide text-muted-foreground">
                        Best price
                    </span>

                    <span class="text-sm font-bold text-foreground whitespace-nowrap">
                        {{ formattedPrice }}
                    </span>
                </div>

                <div class="flex size-5 items-center justify-center rounded-full bg-primary/15">
                    <Star class="size-2.5 text-primary fill-primary" />
                </div>
            </div>

            <Link
                :href="catalog.show(master.match_id)"
                class="flex size-8 items-center justify-center rounded-lg border border-border/50 bg-background/50 text-muted-foreground transition-all hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
            >
                <ChevronRight class="size-4" />
            </Link>
        </div>
    </div>
</template>

<style scoped>
.hide-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>
