<script setup>
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useSource } from '@/composables/useSource';
import { ExternalLink, PackageX, Store, Gem, Box } from 'lucide-vue-next';

const props = defineProps({
    entities: {
        type: Array,
        required: true
    },
    currency: {
        type: String,
        default: 'MDL'
    }
});

console.log(props.entities);

const formatPrice = (price) => {
    if (!price) return '—';
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: props.currency || 'MDL',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(price);
};

const hasDiscount = (entity) => {
    const data = entity.data;
    return data?.old_price && data?.price && data?.price < data?.old_price;
};

const isOutOfStock = (entity) => {
    return entity.data?.is_out_of_stock === true;
};

const sortedEntities = [...props.entities].sort((a, b) => {
    const priceA = a.data.price || Infinity;
    const priceB = b.data.price || Infinity;
    return priceA - priceB;
});

const bestPrice = sortedEntities.length > 0
    ? Math.min(...sortedEntities.map(e => e.data.price || Infinity))
    : null;
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <CardTitle class="text-sm font-medium flex items-center gap-2">
                    <Store class="h-4 w-4" />
                    Price Comparison
                </CardTitle>
                <span class="text-xs text-muted-foreground">
                    {{ sortedEntities.length }} source(s)
                </span>
            </div>
        </CardHeader>
        <CardContent>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-border/50">
                        <th class="text-left py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Source
                        </th>
                        <th class="text-left py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            IMAGE /  ID
                        </th>
                        <th class="text-left py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            TITLE
                        </th>
                        <th class="text-left py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Product / Variant
                        </th>
                        <th class="text-left py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Brand
                        </th>
                        <th class="text-right py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Price
                        </th>
                        <th class="text-center py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Status
                        </th>
                        <th class="text-center py-2.5 px-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Link
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(entity, index) in sortedEntities"
                        :key="entity.id || index"
                        class="border-b border-border/30 transition-colors hover:bg-muted/20"
                        :class="[
                            entity.data?.price === bestPrice && !isOutOfStock(entity)
                                ? 'bg-primary/5'
                                : ''
                        ]"
                    >
                        <!-- Source -->
                        <td class="py-2.5 px-3">
                            <div class="flex items-center gap-2">
                                <img
                                    :src="useSource(entity.source).logo"
                                    class="size-5 rounded object-contain flex-shrink-0"
                                    :alt="useSource(entity.source).label"
                                />
                                <span class="font-medium text-foreground">
                                    {{ useSource(entity.source).label }}
                                </span>
                                <div v-if="entity.data?.price === bestPrice && !isOutOfStock(entity)" title="Best price">
                                    <Gem class="text-primary size-5" />
                                </div>
                            </div>
                        </td>

                        <td class="py-2.5 px-3">
                            <div class="flex items-center gap-3">
                                <div class="aspect-square w-14 overflow-hidden flex items-center justify-center">
                                    <img
                                        v-if="entity.data?.image"
                                        :src="entity.data.image"
                                        :alt="'image-' + entity.source"
                                        loading="lazy"
                                    />
                                    <Box v-else class="size-6 text-muted-foreground/40" />
                                </div>
                                <span class="text-xs text-muted-foreground font-mono truncate max-w-[100px]">
                                    {{ entity.external_id }}
                                </span>
                            </div>
                        </td>

                        <td class="py-2.5 px-3">
                            <span class="text-sm text-foreground">
                                {{ entity.title }}
                            </span>
                        </td>

                        <td class="py-2.5 px-3">
                            <span class="text-sm text-foreground">
                                {{ entity.data.raw?.variant }}
                            </span>
                        </td>

                        <td class="py-2.5 px-3">
                            <span class="text-sm text-muted-foreground">
                                {{ entity.data?.brand ?? '—' }}
                            </span>
                        </td>

                        <!-- Price -->
                        <td class="py-2.5 px-3 text-right">
                            <div class="flex flex-col items-end">
                                <span
                                    v-if="entity.data?.old_price && !isOutOfStock(entity)"
                                    class="text-xs text-muted-foreground line-through"
                                >
                                    {{ formatPrice(entity.data?.old_price) }}
                                </span>

                                <span
                                    class="text-sm font-semibold"
                                    :class="{
                                        'text-primary': entity.data?.price === bestPrice && !isOutOfStock(entity),
                                        'text-muted-foreground': isOutOfStock(entity),
                                        'text-foreground': entity.data?.price !== bestPrice && !isOutOfStock(entity),
                                    }"
                                >
                                    {{ formatPrice(entity.data?.price) }}
                                </span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="py-2.5 px-3 text-center">
                            <div v-if="isOutOfStock(entity)" class="flex items-center justify-center gap-1.5" title="Out of stock">
                                <PackageX class="text-destructive size-4" />
                                <span class="text-xs text-destructive font-medium">Out</span>
                            </div>
                            <Badge
                                v-else-if="hasDiscount(entity)"
                                variant="outline"
                                class="text-[10px] px-1.5 py-0 h-5 border-green-500/30 text-green-600 dark:text-green-400"
                            >
                                {{ entity.data?.discount }}%
                            </Badge>
                            <span v-else class="text-xs text-muted-foreground">—</span>
                        </td>

                        <!-- Link -->
                        <td class="py-2.5 px-3 text-center">
                            <a
                                :href="entity.data?.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-colors"
                            >
                                <ExternalLink class="h-4 w-4" />
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div v-if="sortedEntities.length === 0" class="text-center py-8 text-muted-foreground">
                No prices available for this product
            </div>
        </CardContent>
    </Card>
</template>
