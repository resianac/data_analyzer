<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { LoaderCircle, Search } from 'lucide-vue-next'
import { Popover, PopoverContent, PopoverAnchor } from '@/components/ui/popover'
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group/index.js';
import { KbdGroup, Kbd } from '@/components/ui/kbd/index.js';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },

    loading: {
        type: Boolean,
        default: false,
    },
})

const emits = defineEmits(['update:modelValue'])

const searchQuery = computed({
    get: () => props.modelValue,
    set: (newQuery) => emits('update:modelValue', newQuery),
})

const searchInputRef = ref(null);
const isContentResultOpen = ref(false);

watch(
    () => props.modelValue,
    () => {
        isContentResultOpen.value = true;
    }
)

const openSearch = () => {
    isContentResultOpen.value = true;
    setTimeout(() => {
        searchInputRef.value?.$el.focus();
    }, 100);
};

const closeSearch = () => {
    isContentResultOpen.value = false;
};

const openContentResult = () => {
    if (props.modelValue) {
        isContentResultOpen.value = true;
    }
}

const handleKeydown = (e) => {
    if (e.ctrlKey && e.key.toLowerCase() === 'k') openSearch();
    if (e.key === 'Escape') closeSearch();
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
    <Popover
        :open="isContentResultOpen"
        @update:open="isContentResultOpen = $event"
    >
        <PopoverAnchor as-child>
            <InputGroup>
                <InputGroupInput
                    ref="searchInputRef"
                    v-model="searchQuery"
                    placeholder="Search..."
                    @click="openContentResult()"
                    @keydown="handleKeydown"
                />

                <InputGroupAddon>
                    <LoaderCircle
                        v-if="loading"
                        class="size-4 animate-spin"
                    />

                    <Search v-else/>
                </InputGroupAddon>

                <InputGroupAddon
                    v-if="!loading"
                    align="inline-end"
                >
                    <KbdGroup>
                        <Kbd>Ctrl</Kbd>
                        <span>+</span>
                        <Kbd>K</Kbd>
                    </KbdGroup>
                </InputGroupAddon>
            </InputGroup>
        </PopoverAnchor>
        <PopoverContent
            class="sm:w-[500px] md:w-[750px] lg:w-[900px]"
            align="end"
        >
            <slot
                name="content"
                :loading="loading"
            />
        </PopoverContent>
    </Popover>
</template>
