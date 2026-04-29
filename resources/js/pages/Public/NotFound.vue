<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Home } from 'lucide-vue-next';

import { useT } from '@/composables/useTranslate';

defineProps<{
    status?: number;
    message?: string;
}>();

const { t } = useT();

const suggestions = [
    {
        href: '/projects',
        labelKey: 'nav.projects',
        descKey: 'nav.desc.projects',
    },
    {
        href: '/services',
        labelKey: 'nav.services',
        descKey: 'nav.desc.services',
    },
    {
        href: '/about',
        labelKey: 'nav.about',
        descKey: 'nav.desc.about_me',
    },
    {
        href: '/contact',
        labelKey: 'nav.contact',
        descKey: 'cta.write_to_me',
    },
];

function goBack() {
    if (typeof window !== 'undefined' && window.history.length > 1) {
        window.history.back();

        return;
    }

    router.visit('/');
}
</script>

<template>
    <Head :title="t('errors.404.title')" />

    <section class="flex min-h-[70vh] items-center px-6 py-24 sm:px-8 lg:px-12">
        <div class="mx-auto max-w-[720px]">
            <div
                class="font-mono text-[12px] font-medium tracking-[0.16em] text-muted-foreground uppercase"
            >
                <span class="text-accent">{{ status || 404 }}</span> ·
                {{ t('errors.404.eyebrow') }}
            </div>

            <h1
                class="mt-6 text-[clamp(3rem,8vw,6rem)] leading-[0.95] font-semibold tracking-[-0.03em]"
                style="text-wrap: balance"
            >
                {{ t('errors.404.title') }}<span class="text-accent">.</span>
            </h1>

            <p
                class="mt-8 max-w-[55ch] text-lg leading-relaxed text-muted-foreground"
                style="text-wrap: pretty"
            >
                {{ message || t('errors.404.body') }}
            </p>

            <div class="mt-12 flex flex-wrap gap-4">
                <Link
                    href="/"
                    class="group inline-flex items-center gap-2 bg-foreground px-5 py-3 text-sm font-medium text-background transition-opacity hover:opacity-90 active:scale-[0.98]"
                >
                    <Home class="h-4 w-4" :stroke-width="1.5" />
                    {{ t('errors.404.home') }}
                </Link>
                <button
                    type="button"
                    class="group inline-flex items-center gap-2 border border-border bg-background px-5 py-3 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                    @click="goBack"
                >
                    <ArrowLeft
                        class="h-4 w-4 transition-transform group-hover:-translate-x-0.5"
                        :stroke-width="1.5"
                    />
                    {{ t('errors.404.back') }}
                </button>
            </div>

            <nav class="mt-16 border-t border-border pt-8">
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >{{ t('errors.404.try') }}</span
                >
                <ul
                    class="mt-5 grid grid-cols-1 gap-px bg-border sm:grid-cols-2"
                >
                    <li v-for="r in suggestions" :key="r.href">
                        <Link
                            :href="r.href"
                            class="group flex flex-col bg-background p-4 transition-colors hover:bg-muted/40"
                        >
                            <span
                                class="text-sm font-semibold tracking-[-0.005em]"
                                >{{ t(r.labelKey) }}</span
                            >
                            <span
                                class="mt-1 text-[13px] text-muted-foreground"
                                >{{ t(r.descKey) }}</span
                            >
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>
