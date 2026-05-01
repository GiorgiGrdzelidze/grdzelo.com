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

// URL-canonical i18n: locale lives in the path itself.
// Every public URL is /{locale}/{path}; switching languages is a path rewrite —
// strip the existing /{locale} segment, prepend the new one. No controller
// round-trip needed.
function switchUrl(loc: string): string {
    const url = (page.url as string) || '/';
    const [pathOnly, ...rest] = url.split('?');
    const query = rest.length ? '?' + rest.join('?') : '';

    const stripped = pathOnly.replace(/^\/(en|ka|ru)(?=\/|$)/, '');
    const target = stripped === '' ? `/${loc}` : `/${loc}${stripped}`;

    return target + query;
}
</script>
