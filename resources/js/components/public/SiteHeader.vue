<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ThemeToggle from '@/components/public/ThemeToggle.vue';
import { Button } from '@/components/ui/button';

interface Props {
    settings?: Record<string, any>;
}

defineProps<Props>();
const page = usePage();

const isMobileMenuOpen = ref(false);

const currentUrl = computed(() => page.url);

const navItems = [
    { label: 'Home', href: '/' },
    { label: 'Projects', href: '/projects' },
    { label: 'Blog', href: '/blog' },
    { label: 'Services', href: '/services' },
    { label: 'About', href: '/about' },
    { label: 'Contact', href: '/contact' },
];


function isActive(href: string): boolean {
    if (href === '/') {
        return currentUrl.value === '/';
    }

    return currentUrl.value.startsWith(href);
}

function toggleMobile() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

function closeMobile() {
    isMobileMenuOpen.value = false;
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
                <Link
                    v-for="item in navItems"
                    :key="item.href"
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
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
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
                    <div class="mt-2 border-t border-border/40 pt-2">
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
