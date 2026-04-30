<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FlashToast from '@/components/public/FlashToast.vue';
import SiteFooter from '@/components/public/SiteFooter.vue';
import SiteHeader from '@/components/public/SiteHeader.vue';

const page = usePage();

const settings = computed(
    () => page.props.settings as Record<string, any> | undefined,
);

const pageTitle = computed(() => {
    const seo = page.props.seo as Record<string, any> | undefined;

    return (seo?.title as string) || '';
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <SiteHeader :settings="settings" />

        <main class="flex-1">
            <slot />
        </main>

        <SiteFooter :settings="settings" />

        <FlashToast />
    </div>
</template>
