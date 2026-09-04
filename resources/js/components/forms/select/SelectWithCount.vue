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
            <SelectTrigger>
                <SelectValue :placeholder="placeholder" />
                <SelectInput />
                <SelectTrailingIcon />
                <SelectClear />
            </SelectTrigger>

            <SelectPopover class="p-1 !bg-popover !border !border-border !border-popover-border !text-popover-foreground">
                <SelectListbox>
                    <SelectNoOptions class="!px-3 !py-2 !text-sm !text-muted-foreground" />
                    <SelectOption
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                        :label="option.label"
                        class="!cursor-pointer !rounded-sm !px-2 !py-1.5 !text-sm !outline-none !transition-colors !bg-popover !text-popover-foreground hover:!bg-accent hover:!text-accent-foreground data-[highlighted]:!bg-accent data-[highlighted]:!text-accent-foreground data-[selected]:!bg-primary/10 data-[selected]:!text-primary data-[disabled]:!pointer-events-none data-[disabled]:!opacity-50"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex-1">{{ option.label }}</div>
                            <div class="flex-1">
                            <span v-if="option.count !== undefined" class="!ml-3 !text-xs !text-muted-foreground">
                                {{ option.count }}
                            </span>
                            </div>

                        </div>
                    </SelectOption>
                </SelectListbox>
            </SelectPopover>
        </SelectRoot>
    </div>
</template>
