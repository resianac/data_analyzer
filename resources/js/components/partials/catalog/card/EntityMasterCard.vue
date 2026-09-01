<!-- resources/js/components/entity/ProductCard.vue -->
<script setup>
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { ExternalLink, Box, PackageX } from 'lucide-vue-next';
import { useSource } from '@/composables/useSource.js';
import EntitySourceRow from '@/components/partials/catalog/entity/EntitySourceRow.vue';

const props = defineProps({
    master: {
        type: Object,
        required: true
    }
});

const entities = props.master.entities || [];
const currency = entities[0]?.data?.currency || 'MDL';

const sortedEntities = [...entities].sort((a, b) => {
    const labelA = useSource(a.source).label.value || a.source;
    const labelB = useSource(b.source).label.value || b.source;
    return labelA.localeCompare(labelB);
});

const bestPrice = sortedEntities.length > 0
    ? Math.min(...sortedEntities.map(e => e.data?.price || Infinity))
    : null;
</script>

<template>
    <Card class="overflow-hidden border-border/40 hover:border-primary/30 hover:shadow-md transition-all gap-2 pb-0">
        <div class="relative aspect-[3/2] bg-muted/20 overflow-hidden">
            <div class="flex h-full w-full items-center justify-center text-muted-foreground">
                <Box class="size-10" />
            </div>
        </div>

        <CardContent class="p-3">
            <div class="line-clamp-2 text-sm font-medium leading-tight min-h-[2.5rem]">
                {{ master.title || 'Untitled' }}
            </div>

            <p v-if="master.category" class="text-xs text-muted-foreground uppercase mt-0.5">
                {{ master.category }}
            </p>

            <div class="mt-2 space-y-1">
                <EntitySourceRow
                    v-for="(entity, index) in sortedEntities"
                    :key="entity.id || index"
                    :entity="entity"
                    :is-best-price="entity.data?.price === bestPrice && sortedEntities.length > 1"
                    :currency="currency"
                />
            </div>

            <div v-if="entities.length === 0" class="text-sm text-muted-foreground text-center py-4">
                No prices available
            </div>
        </CardContent>

        <CardFooter class="!py-2 px-3 border-t border-border/40">
            <div class="flex w-full items-center justify-end text-xs text-muted-foreground">
                <span>{{ entities.length }} source(s)</span>
            </div>
        </CardFooter>
    </Card>
</template>
