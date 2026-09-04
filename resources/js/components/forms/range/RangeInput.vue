<script setup>
import { computed, ref, watch } from 'vue'
import { Input } from '@/components/ui/input'

const props = defineProps({
    min: {
        type: Number,
        default: 0,
    },

    max: {
        type: Number,
        default: 10000,
    },

    step: {
        type: Number,
        default: 100,
    },

    modelValue: {
        type: Object,
        default: () => ({
            min: 0,
            max: 10000,
        }),
    },
})

const emit = defineEmits(['update:modelValue'])

const minValue = ref(props.modelValue.min)
const maxValue = ref(props.modelValue.max)

const minPercent = computed(() => {
    return ((minValue.value - props.min) / (props.max - props.min)) * 100
})

const maxPercent = computed(() => {
    return ((maxValue.value - props.min) / (props.max - props.min)) * 100
})

function updateMin(value) {
    value = Number(value)

    if (value > maxValue.value) {
        value = maxValue.value
    }

    minValue.value = value

    emitValue()
}

function updateMax(value) {
    value = Number(value)

    if (value < minValue.value) {
        value = minValue.value
    }

    maxValue.value = value

    emitValue()
}

function emitValue() {
    emit('update:modelValue', {
        min: minValue.value,
        max: maxValue.value,
    })
}

function formatPrice(value) {
    return new Intl.NumberFormat('ru-RU').format(value)
}

watch(
    () => props.modelValue,
    (value) => {
        minValue.value = value.min
        maxValue.value = value.max
    },
    { deep: true }
)
</script>

<template>
    <div class="w-full">
        <!-- Inputs -->
        <div class="mb-5 grid grid-cols-2 gap-3">
            <Input
                type="number"
                :min="min"
                :max="maxValue"
                :step="step"
                :model-value="minValue"
                @update:model-value="updateMin"
            />

            <Input
                type="number"
                :min="minValue"
                :max="max"
                :step="step"
                :model-value="maxValue"
                @update:model-value="updateMax"
            />
        </div>

        <!-- ONE slider -->
        <div class="relative h-6">
            <!-- Base line -->
            <div
                class="absolute left-0 right-0 top-1/2 h-1 -translate-y-1/2 rounded-full bg-muted"
            />

            <!-- Selected range -->
            <div
                class="absolute top-1/2 h-1 -translate-y-1/2 rounded-full bg-primary"
                :style="{
                    left: `${minPercent}%`,
                    right: `${100 - maxPercent}%`,
                }"
            />

            <!-- Min -->
            <input
                type="range"
                :min="min"
                :max="max"
                :step="step"
                v-model.number="minValue"
                @input="updateMin(minValue)"
            >

            <!-- Max -->
            <input
                type="range"
                :min="min"
                :max="max"
                :step="step"
                v-model.number="maxValue"
                @input="updateMax(maxValue)"
            >
        </div>

        <!-- Values -->
        <div class="mt-2 flex justify-between text-sm text-muted-foreground">
            <span>{{ formatPrice(minValue) }}</span>
            <span>{{ formatPrice(maxValue) }}</span>
        </div>
    </div>
</template>

<style scoped>
input[type="range"] {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 24px;

    margin: 0;
    padding: 0;

    appearance: none;
    background: transparent;

    pointer-events: none;
}

input[type="range"]::-webkit-slider-runnable-track {
    height: 4px;
    background: transparent;
}

input[type="range"]::-moz-range-track {
    height: 4px;
    background: transparent;
}

input[type="range"]::-webkit-slider-thumb {
    appearance: none;

    width: 18px;
    height: 18px;

    margin-top: -7px;

    border-radius: 50%;
    border: 2px solid #ffffff;

    background: #10b981;

    cursor: pointer;
    pointer-events: auto;
}

input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;

    border-radius: 50%;
    border: 2px solid #ffffff;

    background: #10b981;

    cursor: pointer;
    pointer-events: auto;
}

input[type="range"]::-moz-range-thumb:hover {
    transform: scale(1.1);
}

/* Dark mode */
.dark input[type="range"]::-webkit-slider-thumb {
    border-color: #1a2a1f;
}

.dark input[type="range"]::-moz-range-thumb {
    border-color: #1a2a1f;
}
</style>
