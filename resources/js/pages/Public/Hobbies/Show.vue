<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Heart } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface GalleryItem {
    url: string;
    alt: string | null;
}

interface Hobby {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    description: string | null;
    icon: string | null;
    cover: string | null;
    gallery: GalleryItem[];
    is_featured: boolean;
}

interface Props {
    hobby: Hobby;
}

defineProps<Props>();
</script>

<template>
    <article class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <Link
                href="/hobbies"
                class="mb-8 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Hobbies
            </Link>

            <!-- Header -->
            <header class="text-center">
                <div
                    v-if="hobby.icon"
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-muted"
                >
                    <i :class="hobby.icon" class="text-3xl" />
                </div>
                <div
                    v-else
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-muted"
                >
                    <Heart class="h-8 w-8 text-muted-foreground" />
                </div>
                <h1 class="text-4xl font-bold tracking-tight">
                    {{ hobby.title }}
                </h1>
                <p
                    v-if="hobby.summary"
                    class="mt-4 text-lg text-muted-foreground"
                >
                    {{ hobby.summary }}
                </p>
                <Badge v-if="hobby.is_featured" variant="secondary" class="mt-4"
                    >Featured</Badge
                >
            </header>

            <!-- Cover Image -->
            <div v-if="hobby.cover" class="mt-10 overflow-hidden rounded-xl">
                <img :src="hobby.cover" :alt="hobby.title" class="w-full" />
            </div>

            <!-- Description -->
            <div
                v-if="hobby.description"
                class="mx-auto prose mt-10 max-w-none prose-neutral dark:prose-invert"
                v-html="hobby.description"
            />

            <!-- Gallery -->
            <div v-if="hobby.gallery?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Gallery</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="(item, idx) in hobby.gallery"
                        :key="idx"
                        class="overflow-hidden rounded-lg"
                    >
                        <img
                            :src="item.url"
                            :alt="item.alt ?? ''"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>
