<script setup>
import { computed } from 'vue';
import VChart from 'vue-echarts';
import { use } from 'echarts/core';
import { LineChart as ELineChart } from 'echarts/charts';
import {
    GridComponent,
    TooltipComponent,
    LegendComponent
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';

use([
    ELineChart,
    GridComponent,
    TooltipComponent,
    CanvasRenderer, LegendComponent
]);

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    series: {
        type: Array,
        default: () => [],
    },
    height: {
        type: String,
        default: '300px',
    },
});

const isDark = computed(() => document.documentElement.classList.contains('dark'));

const option = computed(() => ({
    tooltip: {
        trigger: 'axis',

        backgroundColor: isDark.value ? '#1a2a1f' : '#f5faf7',
        borderColor: isDark.value ? '#2a4a35' : '#d4e8dc',
        borderWidth: 1,
        textStyle: {
            color: isDark.value ? '#b8d9c4' : '#1a4a30',
            fontFamily: 'inherit',
        },
    },
    textStyle: {
        color: isDark.value ? '#8ab89a' : '#2d6b4a',
        fontFamily: 'inherit',
    },

    legend: {
        top: 0,
        left: "left",
        textStyle: {
            color: isDark.value ? '#b8d9c4' : '#1a4a30',
        },
        inactiveColor: isDark.value ? '#4a6a5a' : '#8aaa9a',
    },

    grid: {
        left: 45,
        right: 20,
        top: 50,
        bottom: 50,
    },

    xAxis: {
        type: 'category',
        data: props.labels,
        axisLine: {
            lineStyle: {
                color: isDark.value ? '#2a4a35' : '#d4e8dc',
            },
        },
        axisLabel: {
            color: isDark.value ? '#8ab89a' : '#3a7a5a',
        },
        splitLine: {
            show: false,
        },
    },

    yAxis: {
        type: 'value',
        axisLine: {
            show: false,
        },
        axisLabel: {
            color: isDark.value ? '#8ab89a' : '#3a7a5a',
        },
        splitLine: {
            lineStyle: {
                color: isDark.value ? '#1a2a1f' : '#e8f5ee',
                type: 'dashed',
            },
        },
    },
    series: props.series.map(series => ({
        name: series.label,
        type: 'line',
        data: series.data,
        color: series.color,

        showSymbol: true,
        symbol: 'circle',
        symbolSize: 12,

        smooth: true,

        areaStyle: {
            color: series.color + '25',
        },
    })),
}));
console.log(props.series);
</script>

<template>
    <VChart
        :option="option"
        :style="{ height }"
    />
</template>
