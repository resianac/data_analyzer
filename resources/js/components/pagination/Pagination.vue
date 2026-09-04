<script setup>
import { ref, computed, defineProps, defineEmits, watch } from 'vue';
import PageSizeSelector from "@/components/pagination/PageSizeSelector.vue";
import { Button } from '@/components/ui/button/index.js';

const props = defineProps({
    meta: {
        type: Object,
        required: true,
    },
    links: {
        type: Object,
        required: true,
    },
    showSizer: {
        type: Boolean,
        default: true,
    },
    defaultPageSize: {
        type: Number,
        default: 100,
    }
});

const emit = defineEmits(['page-changed', 'page-size-changed']);
const currentPage = ref(props.meta.current_page || 1);

watch(
    () => props.meta.current_page,
    (newPage) => {
        currentPage.value = newPage || 1;
    },
    { immediate: true }
);

const totalPages = computed(() => props.meta.last_page);
const startIndex = computed(() => props.meta.from || 0);
const endIndex = computed(() => props.meta.to || 0);
const visiblePages = computed(() => {
    const range = 5;
    const start = Math.max(currentPage.value - Math.floor(range / 2), 2);
    const end = Math.min(start + range - 1, totalPages.value - 1);
    const visible = [];
    for (let i = start; i <= end; i++) {
        visible.push(i);
    }
    return visible;
})
const shouldShowEllipsis = computed(() => totalPages.value > 6 && currentPage.value <= totalPages.value - 2);

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        emit('page-changed', page);
    }
};

const goToNextPage = () => {
    if (props.links.next) {
        const nextPage = currentPage.value + 1;
        goToPage(nextPage);
    }
};

const goToPreviousPage = () => {
    if (props.links.prev) {
        const prevPage = currentPage.value - 1;
        goToPage(prevPage);
    }
};

const pageNumbers = computed(() => {
    const pages = [];
    for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
    }
    return pages;
});

const handlePageSize = newSize => {
    emit('page-size-changed', newSize);
}
</script>

<template>
    <div
        class="mt-10 flex items-center justify-between gap-4
        border-t border-theme-pagination-border
        bg-theme-pagination
        px-4 py-3 sm:rounded-b-lg sm:px-6"
    >
        <PageSizeSelector
            :default-page-size="defaultPageSize"
            v-if="showSizer"
            @page-size-changed="handlePageSize"
        />

        <div class="flex flex-1 justify-between sm:hidden">
            <Button
                :disabled="!links.prev"
                @click="goToPreviousPage"
            >
                Previous
            </Button>

            <Button
                :disabled="!links.next"
                @click="goToNextPage"
            >
                Next
            </Button>
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-theme-primary mb-0">
                    Showing
                    <span class="font-medium">{{ startIndex }}</span>
                    to
                    <span class="font-medium">{{ endIndex }}</span>
                    of
                    <span class="font-medium">{{ meta.total }}</span>
                    results
                </p>
            </div>

            <div>
                <nav
                    class="isolate inline-flex gap-1 -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <!-- Previous -->
                    <button
                        class="
                            relative inline-flex items-center rounded-l-md
                            px-2 py-2
                            text-theme-primary
                            ring-1 ring-inset ring-theme-pagination-border
                            hover:bg-theme-pagination-hover
                            focus:z-20 focus:outline-offset-0
                        "
                        :disabled="!links.prev"
                        @click="goToPreviousPage"
                    >
                        <span class="sr-only">Previous</span>

                        <svg
                            class="size-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <!-- First page -->
                    <button
                        @click="goToPage(1)"
                        :class="[
                            `
                                relative inline-flex items-center
                                px-4 py-2
                                text-sm font-semibold
                                text-theme-primary
                                ring-1 ring-inset
                                ring-theme-pagination-border
                                hover:bg-theme-pagination-hover
                                focus:z-20 focus:outline-offset-0
                            `,
                            {
                                'ring-theme-pagination-active bg-theme-pagination-active !text-theme-pagination-active-text': currentPage === 1
                            }
                        ]"
                    >
                        1
                    </button>

                    <!-- Left ellipsis -->
                    <span
                        v-if="visiblePages[0] > 2"
                        class="
                            relative inline-flex items-center
                            px-4 py-2
                            text-sm font-semibold
                            text-theme-muted
                        "
                    >
                        ...
                    </span>

                    <!-- Visible pages -->
                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        :class="[
                            `
                                relative inline-flex items-center
                                px-4 py-2
                                text-sm font-semibold
                                text-theme-primary
                                ring-1 ring-inset
                                ring-theme-pagination-border
                                hover:bg-theme-pagination-hover
                                focus:z-20 focus:outline-offset-0
                            `,
                            {
                                'ring-theme-pagination-active bg-theme-pagination-active !text-theme-pagination-active-text': currentPage === page
                            }
                        ]"
                    >
                        {{ page }}
                    </button>

                    <!-- Right ellipsis -->
                    <span
                        v-if="visiblePages[visiblePages.length - 1] < totalPages - 1"
                        class="
                            relative inline-flex items-center
                            px-4 py-2
                            text-sm font-semibold
                            text-theme-muted
                        "
                    >
                        ...
                    </span>

                    <!-- Last page -->
                    <button
                        v-if="totalPages > 1"
                        @click="goToPage(totalPages)"
                        :class="[
                            `
                                relative inline-flex items-center
                                px-4 py-2
                                text-sm font-semibold
                                text-theme-primary
                                ring-1 ring-inset
                                ring-theme-pagination-border
                                hover:bg-theme-pagination-hover
                                focus:z-20 focus:outline-offset-0
                            `,
                            {
                                'ring-theme-pagination-active bg-theme-pagination-active !text-theme-pagination-active-text':
                                    currentPage === totalPages
                            }
                        ]"
                    >
                        {{ totalPages }}
                    </button>

                    <!-- Next -->
                    <button
                        class="
                            relative inline-flex items-center rounded-r-md
                            px-2 py-2
                            text-theme-primary
                            ring-1 ring-inset ring-theme-pagination-border
                            hover:bg-theme-pagination-hover
                            focus:z-20 focus:outline-offset-0
                        "
                        :disabled="!links.next"
                        @click="goToNextPage"
                    >
                        <span class="sr-only">Next</span>

                        <svg
                            class="size-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 1 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>
