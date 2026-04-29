<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({ inheritAttrs: false });

interface Props {
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: '',
});
</script>

<template>
    <Head :title="title" />
</template>
