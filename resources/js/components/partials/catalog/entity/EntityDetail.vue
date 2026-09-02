<script setup>
import { Badge } from '@/components/ui/badge';
import { useSource } from '@/composables/useSource';
import { Calendar, Tag, ExternalLink, Box } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    master: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('ro-RO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const masterImage = computed(() => props.master.entities.find(i => i.data.image)?.data.image ?? null)

const sources = props.master.entities?.map(e => e.source) || [];
const uniqueSources = [...new Set(sources)];
</script>

<template>
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                <div class="w-full md:w-64 aspect-square bg-muted/30 rounded-lg flex items-center justify-center text-muted-foreground">
                    <img v-if="masterImage" :src="masterImage" alt="product image">
                    <Box v-else class="size-10" />
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1 space-y-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">
                        {{ master.title || 'Untitled' }}
                    </h1>
                    <p v-if="master.category" class="text-sm text-muted-foreground mt-1 uppercase">
                        {{ master.category }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-1.5 text-muted-foreground">
                        <Tag class="h-4 w-4" />
                        <span>{{ uniqueSources.length }} source(s)</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-muted-foreground">
                        <Calendar class="h-4 w-4" />
                        <span>Last Update: {{ formatDate(master.updated_at) }}</span>
                    </div>
                </div>

                <!-- Sources badges -->
                <div class="pt-2">
                    <p class="text-xs font-medium text-muted-foreground tracking-wider mb-1">
                        We found information here:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a
                            v-for="source in uniqueSources"
                            :key="source"
                            :href="useSource(source).website"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded border px-3 py-2 hover:border-primary/30 hover:bg-primary/5"
                        >
                            <img
                                :src="useSource(source).logo"
                                class="size-4 shrink-0 rounded object-contain"
                                :alt="useSource(source).label"
                            />

                            <span class="text-sm font-medium">
                                {{ useSource(source).label }}
                            </span>

                            <ExternalLink class="size-4 shrink-0 text-muted-foreground" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
</template>
