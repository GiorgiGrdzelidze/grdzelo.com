<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
    title?: string;
    description?: string;
    canonical?: string;
    robots?: string;
    og?: {
        title?: string;
        description?: string;
        image?: string;
        type?: string;
    };
    twitter?: {
        title?: string;
        description?: string;
        image?: string;
        card?: string;
    };
    schema?: Record<string, any> | null;
    breadcrumb_title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    description: '',
    canonical: '',
    robots: '',
    breadcrumb_title: '',
});

const schemaScript = props.schema ? JSON.stringify(props.schema) : null;
</script>

<template>
    <Head :title="title">
        <meta v-if="description" name="description" :content="description" />
        <meta v-if="robots" name="robots" :content="robots" />
        <link v-if="canonical" rel="canonical" :href="canonical" />

        <!-- Open Graph -->
        <meta v-if="og?.title" property="og:title" :content="og.title" />
        <meta
            v-if="og?.description"
            property="og:description"
            :content="og.description"
        />
        <meta v-if="og?.image" property="og:image" :content="og.image" />
        <meta v-if="og?.type" property="og:type" :content="og.type" />
        <meta v-if="canonical" property="og:url" :content="canonical" />

        <!-- Twitter Card -->
        <meta
            v-if="twitter?.card"
            name="twitter:card"
            :content="twitter.card"
        />
        <meta
            v-if="twitter?.title"
            name="twitter:title"
            :content="twitter.title"
        />
        <meta
            v-if="twitter?.description"
            name="twitter:description"
            :content="twitter.description"
        />
        <meta
            v-if="twitter?.image"
            name="twitter:image"
            :content="twitter.image"
        />

        <!-- JSON-LD -->
        <!-- eslint-disable vue/no-v-text-v-html-on-component -->
        <component
            v-if="schemaScript"
            :is="'script'"
            type="application/ld+json"
            v-text="schemaScript"
        />
        <!-- eslint-enable vue/no-v-text-v-html-on-component -->
    </Head>
</template>
