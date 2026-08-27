<script setup lang="ts">
import AppLogo from '@/components/partials/layout/app/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import UserMenuContent from '@/components/partials/layout/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Menu, Search, X, ChevronRight, Home } from 'lucide-vue-next';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import InputGroup from "@/components/ui/input-group/InputGroup.vue";
import InputGroupInput from "@/components/ui/input-group/InputGroupInput.vue";
import InputGroupAddon from "@/components/ui/input-group/InputGroupAddon.vue";
import KbdGroup from "@/components/ui/kbd/KbdGroup.vue";
import Kbd from "@/components/ui/kbd/Kbd.vue";

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const searchQuery = ref('');
const isSearchOpen = ref(false);
const searchInput = ref<HTMLInputElement | null>(null);

const performSearch = () => {
    // if (searchQuery.value.trim()) {
        // Используйте Inertia router или window.location
        // window.location.href = `/search?q=${encodeURIComponent(searchQuery.value)}`;
        // Или через Inertia:
        // router.get('/search', { q: searchQuery.value });
    // }
};

const openSearch = () => {
    isSearchOpen.value = true;
    setTimeout(() => {
        searchInput.value?.focus();
    }, 100);
};

const closeSearch = () => {
    isSearchOpen.value = false;
    searchQuery.value = '';
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') performSearch();
    if (e.key === 'Escape') closeSearch();
};

const handleGlobalKeydown = (e: KeyboardEvent) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
    }
};

onMounted(() => { document.addEventListener('keydown', handleGlobalKeydown); });
onUnmounted(() => { document.removeEventListener('keydown', handleGlobalKeydown); });

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'bg-primary/10 text-primary font-medium'
            : 'text-foreground/70 hover:text-foreground hover:bg-muted/50',
);

const mainNavItems: NavItem[] = [
    {
        title: 'Catalog',
        href: dashboard(),
        icon: LayoutGrid,
    },
];
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="mx-auto max-w-7xl px-4">
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <Link :href="dashboard()" class="shrink-0 flex items-center gap-x-2">
                        <AppLogo />
                    </Link>

                    <div class="hidden lg:flex h-full">
                        <NavigationMenu class="flex h-full items-stretch">
                            <NavigationMenuList class="flex h-full items-stretch space-x-1">
                                <NavigationMenuItem
                                    v-for="(item, index) in mainNavItems"
                                    :key="index"
                                    class="relative flex h-full items-center"
                                >
                                    <Link
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                            'h-9 cursor-pointer px-4 rounded-lg text-sm font-medium transition-all',
                                        ]"
                                        :href="item.href"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="mr-2 h-4 w-4"
                                        />
                                        {{ item.title }}
                                    </Link>

                                    <div
                                        v-if="isCurrentRoute(item.href)"
                                        class="absolute bottom-0 left-1/2 h-0.5 w-6 -translate-x-1/2 rounded-full bg-primary"
                                    ></div>
                                </NavigationMenuItem>
                            </NavigationMenuList>
                        </NavigationMenu>
                    </div>
                </div>

                <!-- Поиск (десктоп) -->
                <div class="hidden md:flex flex-1 max-w-xl mx-4 relative">
                    <div class="relative w-full">
                        <InputGroup>
                            <InputGroupInput
                                ref="searchInput"
                                v-model="searchQuery"
                                placeholder="Search..."
                                @keydown="handleKeydown"
                            />
                            <InputGroupAddon>
                                <Search />
                            </InputGroupAddon>
                            <InputGroupAddon align="inline-end">
                                <KbdGroup>
                                    <Kbd>Ctrl</Kbd>
                                    <span>+</span>
                                    <Kbd>K</Kbd>
                                </KbdGroup>
                            </InputGroupAddon>
                        </InputGroup>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <!-- Кнопка поиска (мобильная) -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="md:hidden h-9 w-9 text-muted-foreground hover:text-foreground"
                        @click="openSearch"
                    >
                        <Search class="h-5 w-5" />
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative h-9 w-9 rounded-full p-0 hover:bg-muted"
                            >
                                <Avatar class="h-8 w-8">
                                    <AvatarImage
                                        v-if="auth.user?.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user.name"
                                    />
                                    <AvatarFallback
                                        class="bg-muted text-muted-foreground text-xs font-medium"
                                    >
                                        {{ auth.user ? getInitials(auth.user.name) : 'Войти' }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <div
                v-if="isSearchOpen"
                class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm md:hidden"
                @click.self="closeSearch"
            >
                <div class="container max-w-7xl mx-auto px-4 pt-4">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                ref="searchInput"
                                v-model="searchQuery"
                                type="text"
                                placeholder="Поиск..."
                                class="w-full h-12 rounded-lg border border-input bg-background pl-9 pr-4 text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                @keydown="handleKeydown"
                                autofocus
                            />
                        </div>
                        <Button variant="ghost" size="icon" @click="closeSearch">
                            <X class="h-5 w-5" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs && props.breadcrumbs.length > 1"
            class="border-b border-border/40 bg-gradient-to-r from-muted/5 via-muted/10 to-muted/5"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex h-10 items-center gap-1 text-sm">
                    <ChevronRight class="h-3.5 w-3.5 text-muted-foreground/40" />

                    <Breadcrumbs :breadcrumbs="props.breadcrumbs" />
                </div>
            </div>
        </div>
    </header>
</template>
