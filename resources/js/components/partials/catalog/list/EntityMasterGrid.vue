<script setup>
import EntityMasterCard from '@/components/partials/catalog/card/EntityMasterCard.vue';

const props = defineProps({
    masters: {
        type: Array,
        required: true,
        default: () => []
    }
});
</script>

<template>
    <div v-if="masters.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <EntityMasterCard
            v-for="master in masters"
            :key="master.id"
            :master="master"
        />
    </div>

    <div v-else class="flex flex-col items-center justify-center py-12 text-center">
        <p class="text-muted-foreground">No products found</p>
    </div>
</template>
