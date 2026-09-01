<script setup>
import { computed } from 'vue';
import { useSource } from '@/composables/useSource.js';
import { ExternalLink, PackageX } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge/index.js';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    isBestPrice: {
        type: Boolean,
        default: false
    },
    currency: {
        type: String,
        default: 'MDL'
    }
});

const isOutOfStock = computed(() => props.entity?.data?.is_out_of_stock === true);
const hasDiscount = computed(() => {
    const data = props.entity?.data;
    return data?.old_price && data?.price < data?.old_price;
});

const getDiscountPercent = computed(() => {
    const data = props.entity?.data;
    if (!data?.old_price || !data?.price) return 0;
    return Math.round((1 - data.price / data.old_price) * 100);
});

const formatPrice = (price) => {
    if (!price) return '—';
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: props.currency || 'MDL',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(price);
};

const { label, logo } = useSource(props.entity.source);
</script>

<template>
    <div
        class="flex items-center justify-between px-2 py-1.5 rounded transition-colors relative overflow-hidden"
        :class="[
            isBestPrice && !isOutOfStock
                ? 'bg-primary/5 border border-primary/15'
                : 'bg-muted/30 border border-transparent',
            isOutOfStock
                ? '!bg-muted/30 border border-transparent opacity-80'
                : ''
        ]"
        :style="isOutOfStock
            ? {backgroundImage: 'repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(0,0,0,0.1) 8px, rgba(0,0,0,0.1) 16px)'}
            : {}"
    >
        <a
            :href="entity.data?.url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 text-xs font-medium text-muted-foreground hover:text-foreground transition-colors group flex-shrink-0 min-w-[80px]"
        >
            <img
                class="size-4 rounded object-contain flex-shrink-0"
                :src="logo"
                :alt="label"
                loading="lazy"
            />
            <span class="truncate">{{ label }}</span>
            <ExternalLink class="h-2.5 w-2.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" />
        </a>

        <!-- Price + badges -->
        <div class="flex items-center gap-2 flex-shrink-0">
            <Badge
                v-if="hasDiscount"
                variant="outline"
                class="text-[10px] px-1.5 py-0 h-5 border-green-500/30 text-green-600 dark:text-green-400"
            >
                -{{ getDiscountPercent }}%
            </Badge>

            <div v-if="isOutOfStock" title="Out of stock">
                <PackageX class="text-destructive size-4" />
            </div>

            <span
                class="text-sm font-semibold"
                :class="{
                    'text-primary': isBestPrice && !isOutOfStock,
                    'text-muted-foreground': !isBestPrice && isOutOfStock,
                    'text-foreground': !isBestPrice && !isOutOfStock,
                }"
            >
                {{ formatPrice(entity.data?.price) }}
            </span>
        </div>
    </div>
</template>
