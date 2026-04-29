<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

import { useT } from '@/composables/useTranslate';

interface SocialLinkItem {
    platform: string;
    label: string | null;
    url: string;
    username: string | null;
    icon: string | null;
}

interface FooterLink {
    key: string;
    href: string;
}

interface FooterColumn {
    titleKey: string;
    items: FooterLink[];
}

interface Props {
    settings?: Record<string, unknown>;
}

defineProps<Props>();

const { t } = useT();
const page = usePage();
const socialLinks = page.props.socialLinks as SocialLinkItem[] | undefined;

const year = new Date().getFullYear();

const cols: FooterColumn[] = [
    {
        titleKey: 'footer.work',
        items: [
            { key: 'nav.projects', href: '/projects' },
            { key: 'nav.repositories', href: '/repositories' },
            { key: 'nav.services', href: '/services' },
        ],
    },
    {
        titleKey: 'footer.about',
        items: [
            { key: 'nav.about_me', href: '/about' },
            { key: 'nav.skills', href: '/skills' },
            { key: 'nav.experience', href: '/experience' },
            { key: 'nav.education', href: '/education' },
        ],
    },
    {
        titleKey: 'footer.misc',
        items: [
            { key: 'nav.blog', href: '/blog' },
            { key: 'nav.gallery', href: '/gallery' },
            { key: 'nav.hobbies', href: '/hobbies' },
            { key: 'nav.contact', href: '/contact' },
        ],
    },
];
</script>

<template>
    <footer class="border-t border-border bg-background">
        <div class="mx-auto max-w-[1200px] px-6 sm:px-8 lg:px-12">
            <div class="grid gap-12 py-16 md:grid-cols-[5fr_7fr] md:py-20">
                <div>
                    <Link
                        href="/"
                        class="inline-flex items-baseline text-foreground transition-opacity hover:opacity-80"
                        aria-label="grdzelo home"
                    >
                        <span
                            class="font-mono text-2xl font-medium tracking-[-0.04em]"
                            >grdzelo</span
                        >
                        <span
                            class="cursor-blink ml-1 inline-block h-[0.85em] w-[0.5em] translate-y-[0.05em] bg-accent"
                            aria-hidden="true"
                        />
                    </Link>
                    <p
                        class="mt-6 max-w-md text-base leading-relaxed text-muted-foreground"
                        style="text-wrap: pretty"
                    >
                        {{ t('footer.tagline') }}
                    </p>
                    <div class="mt-8 flex items-center gap-3">
                        <span
                            class="inline-block h-2 w-2 rounded-full bg-accent"
                            aria-hidden="true"
                        />
                        <span
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ t('hero.status') }} ·
                            {{ t('hero.location') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 md:grid-cols-3">
                    <div v-for="col in cols" :key="col.titleKey">
                        <h3
                            class="mb-5 font-mono text-[11px] font-medium tracking-[0.16em] text-muted-foreground uppercase"
                        >
                            {{ t(col.titleKey) }}
                        </h3>
                        <ul class="flex flex-col gap-3">
                            <li v-for="item in col.items" :key="item.href">
                                <Link
                                    :href="item.href"
                                    class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t(item.key) }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div
                v-if="socialLinks?.length"
                class="flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-border py-6"
            >
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.16em] text-muted-foreground uppercase"
                >
                    {{ t('footer.elsewhere') }}
                </span>
                <a
                    v-for="link in socialLinks"
                    :key="link.url"
                    :href="link.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-mono text-[11px] font-medium tracking-[0.14em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                    :aria-label="link.label || link.platform"
                >
                    {{ link.label || link.platform }}
                </a>
            </div>

            <div
                class="flex flex-col gap-4 border-t border-border py-8 sm:flex-row sm:items-center sm:justify-between"
            >
                <p
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    © {{ year }} Giorgi Grdzelidze · Tbilisi
                </p>
                <p
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('footer.colophon') }}
                </p>
            </div>
        </div>
    </footer>
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
