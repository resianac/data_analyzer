<script setup>
import {
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectInput,
    SelectTrailingIcon,
    SelectPopover,
    SelectListbox,
    SelectOption,
    SelectNoOptions,
    SelectClear,
} from 'vue3-select-component/primitives';
import { Check, CircleX } from '@lucide/vue';

defineProps({
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
    searchable: {
        type: Boolean,
        default: true,
    },
});

const model = defineModel();
</script>

<template>
    <div data-assembled-select>
        <SelectRoot
            v-model="model"
            :multiple="multiple"
            :clearable="clearable"
            :searchable="searchable"
        >
            <SelectTrigger
                class="!border-input !rounded-md !bg-input/30 px-3 py-1.5 text-sm !text-foreground"
            >
                <SelectValue class="!text-foreground" :placeholder="placeholder">
                    <template #tag-remove>
                        <CircleX class="text-primary-foreground fill-transparent hover:text-destructive"/>
                    </template>
                </SelectValue>
                <SelectInput class="!text-foreground" />
                <SelectTrailingIcon class="!text-muted-foreground" />
                <SelectClear class="h-4 w-4 shrink-0 !text-muted-foreground hover:!text-foreground" />
            </SelectTrigger>

            <SelectPopover class="p-1 !bg-popover !border !border-border !border-popover-border !text-popover-foreground">
                <SelectListbox class="!space-y-1">
                    <SelectNoOptions class="!px-3 !py-2 !text-sm !text-muted-foreground" />
                    <SelectOption
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                        :label="option.label"
                        class="!cursor-pointer !rounded-sm !px-2 !py-1.5 !text-sm !bg-popover aria-selected:!bg-accent !text-popover-foreground hover:!bg-secondary hover:!text-accent-foreground"
                    >
                        <div class="grid grid-cols-[75px_1fr] gap-1">
                            <div class="">{{ option.label }}</div>
                            <div class="">
                            <span v-if="option.count !== undefined" class="!text-xs !text-muted-foreground">
                                {{ option.count }}
                            </span>
                            </div>
                        </div>
                        <template #checkmark>
                            <Check class="text-muted-foreground size-4" />
                        </template>
                    </SelectOption>
                </SelectListbox>
            </SelectPopover>
        </SelectRoot>
    </div>
</template>

<style>
[data-assembled-select] {
    --vs-font-size: 14px;

    --vs-multi-value-background-color: var(--muted-foreground);
    --vs-multi-value-label-text-color: var(--primary-foreground);
    --vs-multi-value-border-radius: 6px;

    --vs-multi-value-xmark-color: transparent;
    --vs-multi-value-delete-hover-background-color: transparent;
    --vs-multi-value-xmark-hover-color: transparent;
}

</style>
