<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SeoHead from '@/components/public/SeoHead.vue';
import SiteFooter from '@/components/public/SiteFooter.vue';
import SiteHeader from '@/components/public/SiteHeader.vue';

const page = usePage();

const seo = computed(() => page.props.seo as Record<string, any> | undefined);
const settings = computed(
    () => page.props.settings as Record<string, any> | undefined,
);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <SeoHead v-if="seo" v-bind="seo" />

        <SiteHeader :settings="settings" />

        <main class="flex-1">
            <slot />
        </main>

        <SiteFooter :settings="settings" />
    </div>
</template>
