<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import LanguageSwitcher from '@/components/public/LanguageSwitcher.vue';
import ThemeToggle from '@/components/public/ThemeToggle.vue';
import { useT } from '@/composables/useTranslate';

interface NavChild {
    key: string;
    href: string;
    descKey?: string;
}

interface NavItem {
    key: string;
    href?: string;
    children?: NavChild[];
}

interface Props {
    settings?: Record<string, unknown>;
}

defineProps<Props>();

const { t } = useT();
const page = usePage();

const isMobileMenuOpen = ref(false);
const openDropdown = ref<string | null>(null);

const currentUrl = computed(() => page.url);

const navItems: NavItem[] = [
    { key: 'nav.home', href: '/' },
    {
        key: 'nav.work',
        children: [
            {
                key: 'nav.projects',
                href: '/projects',
                descKey: 'nav.desc.projects',
            },
            {
                key: 'nav.repositories',
                href: '/repositories',
                descKey: 'nav.desc.repositories',
            },
            {
                key: 'nav.services',
                href: '/services',
                descKey: 'nav.desc.services',
            },
        ],
    },
    { key: 'nav.blog', href: '/blog' },
    {
        key: 'nav.about',
        children: [
            {
                key: 'nav.about_me',
                href: '/about',
                descKey: 'nav.desc.about_me',
            },
            {
                key: 'nav.skills',
                href: '/skills',
                descKey: 'nav.desc.skills',
            },
            {
                key: 'nav.experience',
                href: '/experience',
                descKey: 'nav.desc.experience',
            },
            {
                key: 'nav.education',
                href: '/education',
                descKey: 'nav.desc.education',
            },
            {
                key: 'nav.hobbies',
                href: '/hobbies',
                descKey: 'nav.desc.hobbies',
            },
        ],
    },
    { key: 'nav.gallery', href: '/gallery' },
    { key: 'nav.contact', href: '/contact' },
];

function isActive(href: string): boolean {
    if (href === '/') {
        return currentUrl.value === '/';
    }

    return currentUrl.value === href || currentUrl.value.startsWith(`${href}/`);
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
    openDropdown.value = null;
}

function toggleDropdown(key: string) {
    openDropdown.value = openDropdown.value === key ? null : key;
}
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b border-border bg-background/85 backdrop-blur supports-[backdrop-filter]:bg-background/70"
    >
        <div
            class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-6 sm:px-8 lg:px-12"
        >
            <Link
                href="/"
                class="inline-flex items-baseline text-foreground transition-opacity hover:opacity-80"
                aria-label="grdzelo home"
            >
                <span class="font-mono text-base font-medium tracking-[-0.04em]"
                    >grdzelo</span
                >
                <span
                    class="cursor-blink ml-0.5 inline-block h-[0.85em] w-[0.5em] translate-y-[0.05em] bg-accent"
                    aria-hidden="true"
                />
            </Link>

            <nav
                class="hidden items-center md:flex"
                :aria-label="t('nav.home')"
            >
                <template v-for="item in navItems" :key="item.key">
                    <Link
                        v-if="item.href"
                        :href="item.href"
                        :class="[
                            'relative px-4 py-2 font-mono text-xs font-medium tracking-[0.12em] uppercase transition-colors',
                            isActive(item.href)
                                ? 'text-foreground'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                        :aria-current="isActive(item.href) ? 'page' : undefined"
                    >
                        {{ t(item.key) }}
                        <span
                            v-if="isActive(item.href)"
                            class="absolute inset-x-4 -bottom-px h-px bg-accent"
                            aria-hidden="true"
                        />
                    </Link>

                    <div v-else class="group relative">
                        <button
                            type="button"
                            :class="[
                                'relative inline-flex items-center gap-1 px-4 py-2 font-mono text-xs font-medium tracking-[0.12em] uppercase transition-colors',
                                isGroupActive(item)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            ]"
                            :aria-expanded="
                                openDropdown === item.key ? 'true' : 'false'
                            "
                        >
                            {{ t(item.key) }}
                            <ChevronDown
                                class="h-3 w-3 transition-transform group-hover:rotate-180"
                                :stroke-width="1.5"
                            />
                            <span
                                v-if="isGroupActive(item)"
                                class="absolute inset-x-4 -bottom-px h-px bg-accent"
                                aria-hidden="true"
                            />
                        </button>
                        <div
                            class="invisible absolute top-full left-0 z-50 min-w-[260px] pt-2 opacity-0 transition-all group-focus-within:visible group-focus-within:opacity-100 group-hover:visible group-hover:opacity-100"
                        >
                            <div
                                class="border border-border bg-popover p-1 shadow-md"
                            >
                                <Link
                                    v-for="child in item.children"
                                    :key="child.href"
                                    :href="child.href"
                                    :class="[
                                        'block px-3 py-2.5 transition-colors hover:bg-muted',
                                        isActive(child.href)
                                            ? 'text-foreground'
                                            : 'text-foreground/90',
                                    ]"
                                    :aria-current="
                                        isActive(child.href)
                                            ? 'page'
                                            : undefined
                                    "
                                >
                                    <span
                                        class="block font-mono text-[11px] font-medium tracking-[0.14em] uppercase"
                                        >{{ t(child.key) }}</span
                                    >
                                    <span
                                        v-if="child.descKey"
                                        class="mt-0.5 block text-xs text-muted-foreground"
                                        >{{ t(child.descKey) }}</span
                                    >
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </nav>

            <div class="flex items-center gap-1">
                <LanguageSwitcher class="hidden sm:inline-flex" />
                <span
                    class="mx-1 hidden h-5 w-px bg-border md:block"
                    aria-hidden="true"
                />
                <ThemeToggle />

                <Link
                    href="/contact"
                    class="ml-2 hidden items-center gap-2 bg-foreground px-4 py-2 font-mono text-[11px] font-medium tracking-[0.14em] text-background uppercase transition-opacity hover:opacity-90 active:scale-[0.98] md:inline-flex"
                >
                    {{ t('cta.write_to_me') }}
                </Link>

                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center text-foreground hover:bg-muted md:hidden"
                    :aria-label="t('nav.home')"
                    :aria-expanded="isMobileMenuOpen ? 'true' : 'false'"
                    @click="toggleMobile"
                >
                    <X v-if="isMobileMenuOpen" class="h-5 w-5" />
                    <Menu v-else class="h-5 w-5" />
                    <span class="sr-only">Toggle menu</span>
                </button>
            </div>
        </div>

        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-[640px] opacity-100"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="max-h-[640px] opacity-100"
            leave-to-class="max-h-0 opacity-0"
        >
            <div
                v-if="isMobileMenuOpen"
                class="overflow-hidden border-t border-border md:hidden"
            >
                <nav class="flex flex-col gap-1 px-6 py-4">
                    <template v-for="item in navItems" :key="item.key">
                        <Link
                            v-if="item.href"
                            :href="item.href"
                            :class="[
                                'px-3 py-2.5 font-mono text-xs font-medium tracking-[0.12em] uppercase transition-colors',
                                isActive(item.href)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            ]"
                            @click="closeMobile"
                        >
                            {{ t(item.key) }}
                        </Link>

                        <div v-else>
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-3 py-2.5 font-mono text-xs font-medium tracking-[0.12em] uppercase transition-colors"
                                :class="
                                    isGroupActive(item)
                                        ? 'text-foreground'
                                        : 'text-muted-foreground hover:text-foreground'
                                "
                                :aria-expanded="
                                    openDropdown === item.key ? 'true' : 'false'
                                "
                                @click="toggleDropdown(item.key)"
                            >
                                {{ t(item.key) }}
                                <ChevronDown
                                    class="h-3.5 w-3.5 transition-transform"
                                    :stroke-width="1.5"
                                    :class="{
                                        'rotate-180': openDropdown === item.key,
                                    }"
                                />
                            </button>
                            <div
                                v-if="openDropdown === item.key"
                                class="mt-1 ml-3 flex flex-col gap-1 border-l border-border pl-3"
                            >
                                <Link
                                    v-for="child in item.children"
                                    :key="child.href"
                                    :href="child.href"
                                    :class="[
                                        'px-3 py-2 text-sm transition-colors',
                                        isActive(child.href)
                                            ? 'text-foreground'
                                            : 'text-muted-foreground hover:text-foreground',
                                    ]"
                                    @click="closeMobile"
                                >
                                    {{ t(child.key) }}
                                </Link>
                            </div>
                        </div>
                    </template>

                    <div
                        class="mt-3 flex items-center justify-between border-t border-border pt-3"
                    >
                        <LanguageSwitcher />
                        <Link
                            href="/contact"
                            class="inline-flex items-center bg-foreground px-4 py-2 font-mono text-[11px] font-medium tracking-[0.14em] text-background uppercase"
                            @click="closeMobile"
                        >
                            {{ t('cta.write_to_me') }}
                        </Link>
                    </div>
                </nav>
            </div>
        </Transition>
    </header>
</template>

<style scoped>
.cursor-blink {
    animation: cursor-blink 1s steps(2, end) infinite;
}

@keyframes cursor-blink {
    50% {
        opacity: 0;
    }
}
</style>
