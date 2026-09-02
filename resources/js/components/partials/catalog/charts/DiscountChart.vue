<script setup>
import { computed } from 'vue';
import LineChart from '@/components/charts/LineChart.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Percent, Gift } from 'lucide-vue-next';
import { useSource } from '@/composables/useSource.js';

const props = defineProps({
    metrics: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: 'Discount History',
    },
});

const chartData = computed(() => {
    const groups = {};
    props.metrics.forEach(metric => {
        const source = metric.source;
        if (!groups[source]) {
            groups[source] = [];
        }
        groups[source].push({
            value: metric.value,
            label: metric.created_at,
        });
    });

    Object.keys(groups).forEach(source => {
        groups[source].sort((a, b) => new Date(a.label) - new Date(b.label));
    });

    const allDates = [...new Set(props.metrics.map(m => m.created_at))].sort();

    const series = Object.keys(groups).map(source => {
        const data = groups[source];
        const dateMap = {};
        data.forEach(d => {
            dateMap[d.label] = d.value;
        });

        return {
            name: source,
            label: useSource(source).label,
            color: useSource(source).color,
            data: allDates.map(date => ({
                value: dateMap[date] !== undefined ? dateMap[date] : null,
                label: date,
            })),
        };
    });

    return {
        labels: allDates,
        series,
    };
});

const statistics = computed(() => {
    const allValues = props.metrics
        .filter(m => m.value !== null && m.value !== undefined)
        .map(m => m.value);

    if (allValues.length === 0) {
        return {
            min: null,
            max: null,
            avg: null,
            current: null,
            change: null,
        };
    }

    const min = Math.min(...allValues);
    const max = Math.max(...allValues);
    const avg = allValues.reduce((a, b) => a + b, 0) / allValues.length;
    const current = allValues[allValues.length - 1];

    return {
        min,
        max,
        avg: Math.round(avg * 100) / 100,
        current,
    };
});

const hasData = computed(() => props.metrics.length > 0);

const formatDiscount = (value) => {
    if (value === null || value === undefined) return '—';
    return `${Math.round(Math.abs(value))}%`;
};
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <CardTitle class="text-sm font-medium flex items-center gap-2">
                    <Gift class="h-4 w-4 text-muted-foreground" />
                    {{ title }}
                </CardTitle>

                <!-- Mini stats -->
                <div v-if="hasData" class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-muted-foreground">Min:</span>
                        <span class="font-medium text-foreground">
                            {{ formatDiscount(statistics.min) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-muted-foreground">Max:</span>
                        <span class="font-medium text-foreground">
                            {{ formatDiscount(statistics.max) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-muted-foreground">Avg:</span>
                        <span class="font-medium text-foreground">
                            {{ formatDiscount(statistics.avg) }}
                        </span>
                    </div>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div v-if="hasData" class="w-full">
                <LineChart
                    :labels="chartData.labels"
                    :series="chartData.series"
                />
            </div>

            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <Percent class="h-12 w-12 text-muted-foreground/30 mb-3" />
                <p class="text-sm text-muted-foreground">No discount data available</p>
                <p class="text-xs text-muted-foreground/70 mt-1">Discount history will appear here when available</p>
            </div>
        </CardContent>
    </Card>
</template>
