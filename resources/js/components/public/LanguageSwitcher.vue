<template>
    <div
        role="group"
        :aria-label="t('a11y.language')"
        class="inline-flex items-center font-mono text-[11px] font-medium tracking-[0.14em] uppercase"
    >
        <template v-for="(loc, idx) in locales" :key="loc">
            <span
                v-if="idx > 0"
                aria-hidden="true"
                class="mx-1 text-muted-foreground/40"
                >/</span
            >
            <Link
                :href="switchUrl(loc)"
                preserve-scroll
                replace
                :class="[
                    'px-1 py-1 transition-colors',
                    loc === active
                        ? 'text-foreground'
                        : 'text-muted-foreground hover:text-foreground',
                ]"
                :aria-current="loc === active ? 'true' : undefined"
            >
                {{ loc.toUpperCase() }}
            </Link>
        </template>
    </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { useT } from '@/composables/useTranslate';

const { t } = useT();
const page = usePage();

const locales = ['en', 'ka', 'ru'] as const;
const active = computed(() => (page.props.locale as string) || 'en');

function switchUrl(loc: string): string {
    const ret = encodeURIComponent((page.url as string) || '/');

    return `/locale/${loc}?return=${ret}`;
}
</script>
