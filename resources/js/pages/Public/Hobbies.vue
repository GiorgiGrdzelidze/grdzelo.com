<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface HobbyItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    description: string | null;
    image: string | null;
    icon: string | null;
}

interface Props {
    hobbies: HobbyItem[];
}

defineProps<Props>();
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Hobbies & Interests</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Beyond code — what I enjoy when I'm not building software.
                </p>
            </div>

            <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="hobby in hobbies"
                    :key="hobby.id"
                    :href="`/hobbies/${hobby.slug}`"
                    class="group"
                >
                    <Card class="h-full overflow-hidden transition-all hover:shadow-lg hover:border-foreground/20">
                        <div v-if="hobby.image" class="aspect-video overflow-hidden">
                            <img :src="`/storage/${hobby.image}`" :alt="hobby.title" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        </div>
                        <div v-else class="flex aspect-video items-center justify-center bg-muted">
                            <Heart class="h-10 w-10 text-muted-foreground" />
                        </div>
                        <CardHeader>
                            <CardTitle class="group-hover:text-primary/80 transition-colors">{{ hobby.title }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p v-if="hobby.summary" class="text-sm text-muted-foreground leading-relaxed">{{ hobby.summary }}</p>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <div v-if="!hobbies.length" class="mt-14 text-center">
                <Heart class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">Hobbies coming soon</p>
            </div>
        </div>
    </section>
</template>
