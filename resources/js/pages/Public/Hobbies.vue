<script setup lang="ts">
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
                <Card v-for="hobby in hobbies" :key="hobby.id" class="overflow-hidden">
                    <div v-if="hobby.image" class="aspect-video overflow-hidden">
                        <img :src="`/storage/${hobby.image}`" :alt="hobby.title" class="h-full w-full object-cover" />
                    </div>
                    <div v-else class="flex aspect-video items-center justify-center bg-muted">
                        <Heart class="h-10 w-10 text-muted-foreground" />
                    </div>
                    <CardHeader>
                        <CardTitle>{{ hobby.title }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="hobby.summary" class="text-sm text-muted-foreground leading-relaxed">{{ hobby.summary }}</p>
                    </CardContent>
                </Card>
            </div>

            <div v-if="!hobbies.length" class="mt-14 text-center">
                <Heart class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">Hobbies coming soon</p>
            </div>
        </div>
    </section>
</template>
