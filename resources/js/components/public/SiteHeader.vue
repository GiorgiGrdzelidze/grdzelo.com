<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ThemeToggle from '@/components/public/ThemeToggle.vue';
import { Button } from '@/components/ui/button';

interface NavItem {
    label: string;
    href?: string;
    children?: { label: string; href: string; description?: string }[];
}

interface Props {
    settings?: Record<string, any>;
}

defineProps<Props>();
const page = usePage();

const isMobileMenuOpen = ref(false);
const openDropdown = ref<string | null>(null);

const currentUrl = computed(() => page.url);

const navItems: NavItem[] = [
    { label: 'Home', href: '/' },
    {
        label: 'Work',
        children: [
            { label: 'Projects', href: '/projects', description: 'Featured work & case studies' },
            { label: 'Repositories', href: '/repositories', description: 'Open source & GitHub' },
            { label: 'Services', href: '/services', description: 'What I can help with' },
        ],
    },
    { label: 'Blog', href: '/blog' },
    {
        label: 'About',
        children: [
            { label: 'About Me', href: '/about', description: 'Background & story' },
            { label: 'Skills', href: '/skills', description: 'Technologies & expertise' },
            { label: 'Experience', href: '/experience', description: 'Work history' },
            { label: 'Education', href: '/education', description: 'Academic background' },
            { label: 'Hobbies', href: '/hobbies', description: 'Interests & passions' },
        ],
    },
    { label: 'Gallery', href: '/gallery' },
    { label: 'Contact', href: '/contact' },
];

function isActive(href: string): boolean {
    if (href === '/') {
        return currentUrl.value === '/';
    }

    return currentUrl.value.startsWith(href);
}

function isGroupActive(item: NavItem): boolean {
    if (item.href) {
        return isActive(item.href);
    }

    return item.children?.some((child) => isActive(child.href)) ?? false;
}

function toggleMobile() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

function closeMobile() {
    isMobileMenuOpen.value = false;
}

function toggleDropdown(label: string) {
    openDropdown.value = openDropdown.value === label ? null : label;
}
</script>

<template>
    <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/80 backdrop-blur-xl supports-[backdrop-filter]:bg-background/60">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Brand -->
            <Link href="/" class="flex items-center gap-2 font-semibold tracking-tight transition-colors hover:text-foreground/80">
                <span class="text-lg">{{ settings?.brand_name || 'grdzelo' }}</span>
            </Link>

            <!-- Desktop Nav -->
            <nav class="hidden items-center gap-1 md:flex">
                <template v-for="item in navItems" :key="item.label">
                    <!-- Simple link -->
                    <Link
                        v-if="item.href"
                        :href="item.href"
                        class="rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                        :class="[
                            isActive(item.href)
                                ? 'bg-accent text-accent-foreground'
                                : 'text-muted-foreground',
                        ]"
                    >
                        {{ item.label }}
                    </Link>

                    <!-- Dropdown -->
                    <div v-else class="group relative">
                        <button
                            class="flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                            :class="[
                                isGroupActive(item)
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground',
                            ]"
                        >
                            {{ item.label }}
                            <ChevronDown class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" />
                        </button>
                        <div class="invisible absolute left-0 top-full z-50 min-w-[220px] pt-2 opacity-0 transition-all group-hover:visible group-hover:opacity-100">
                            <div class="rounded-lg border border-border/60 bg-popover p-2 shadow-lg">
                                <Link
                                    v-for="child in item.children"
                                    :key="child.href"
                                    :href="child.href"
                                    class="block rounded-md px-3 py-2.5 transition-colors hover:bg-accent"
                                    :class="[
                                        isActive(child.href)
                                            ? 'bg-accent/50'
                                            : '',
                                    ]"
                                >
                                    <span class="block text-sm font-medium">{{ child.label }}</span>
                                    <span v-if="child.description" class="block text-xs text-muted-foreground">{{ child.description }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center gap-2">
                <ThemeToggle />

                <!-- CTA Button (desktop) -->
                <Button as-child variant="default" size="sm" class="hidden md:inline-flex">
                    <Link href="/contact">
                        {{ settings?.default_cta_text || "Let's Talk" }}
                    </Link>
                </Button>

                <!-- Mobile Menu Toggle -->
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-9 w-9 md:hidden"
                    @click="toggleMobile"
                >
                    <X v-if="isMobileMenuOpen" class="h-5 w-5" />
                    <Menu v-else class="h-5 w-5" />
                    <span class="sr-only">Toggle menu</span>
                </Button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-96 opacity-100"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="max-h-96 opacity-100"
            leave-to-class="max-h-0 opacity-0"
        >
            <div v-if="isMobileMenuOpen" class="overflow-hidden border-t border-border/40 md:hidden">
                <nav class="flex flex-col gap-1 px-4 py-3">
                    <template v-for="item in navItems" :key="item.label">
                        <!-- Simple link -->
                        <Link
                            v-if="item.href"
                            :href="item.href"
                            class="rounded-md px-3 py-2.5 text-sm font-medium transition-colors hover:bg-accent"
                            :class="[
                                isActive(item.href)
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground',
                            ]"
                            @click="closeMobile"
                        >
                            {{ item.label }}
                        </Link>

                        <!-- Collapsible group -->
                        <div v-else>
                            <button
                                class="flex w-full items-center justify-between rounded-md px-3 py-2.5 text-sm font-medium transition-colors hover:bg-accent"
                                :class="[
                                    isGroupActive(item)
                                        ? 'text-accent-foreground'
                                        : 'text-muted-foreground',
                                ]"
                                @click="toggleDropdown(item.label)"
                            >
                                {{ item.label }}
                                <ChevronDown
                                    class="h-4 w-4 transition-transform"
                                    :class="{ 'rotate-180': openDropdown === item.label }"
                                />
                            </button>
                            <div
                                v-if="openDropdown === item.label"
                                class="ml-3 mt-1 flex flex-col gap-1 border-l border-border/40 pl-3"
                            >
                                <Link
                                    v-for="child in item.children"
                                    :key="child.href"
                                    :href="child.href"
                                    class="rounded-md px-3 py-2 text-sm transition-colors hover:bg-accent"
                                    :class="[
                                        isActive(child.href)
                                            ? 'bg-accent/50 font-medium'
                                            : 'text-muted-foreground',
                                    ]"
                                    @click="closeMobile"
                                >
                                    {{ child.label }}
                                </Link>
                            </div>
                        </div>
                    </template>

                    <div class="mt-3 border-t border-border/40 pt-3">
                        <Button as-child variant="default" size="sm" class="w-full">
                            <Link href="/contact" @click="closeMobile">
                                {{ settings?.default_cta_text || "Let's Talk" }}
                            </Link>
                        </Button>
                    </div>
                </nav>
            </div>
        </Transition>
    </header>
</template>
