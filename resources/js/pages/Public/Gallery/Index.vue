<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Camera, MapPin } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';

interface AlbumItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    cover: string | null;
    photo_count: number;
    location: string | null;
    taken_at: string | null;
    is_featured: boolean;
}

interface Props {
    albums: AlbumItem[];
    featured: AlbumItem[];
}

defineProps<Props>();
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold tracking-tight">Gallery</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    A collection of photos from my travels and experiences
                </p>
            </div>

            <!-- Featured Albums -->
            <div v-if="featured.length" class="mb-16">
                <h2 class="mb-6 text-2xl font-semibold">Featured Albums</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <Link
                        v-for="album in featured"
                        :key="album.id"
                        :href="`/gallery/${album.slug}`"
                        class="group relative aspect-[4/3] overflow-hidden rounded-xl"
                    >
                        <img
                            v-if="album.cover"
                            :src="album.cover"
                            :alt="album.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-muted"
                        >
                            <Camera class="h-12 w-12 text-muted-foreground" />
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                        />
                        <div
                            class="absolute right-0 bottom-0 left-0 p-6 text-white"
                        >
                            <h3 class="text-xl font-semibold">
                                {{ album.title }}
                            </h3>
                            <div
                                class="mt-2 flex items-center gap-4 text-sm text-white/80"
                            >
                                <span
                                    v-if="album.photo_count"
                                    class="flex items-center gap-1"
                                >
                                    <Camera class="h-4 w-4" />
                                    {{ album.photo_count }} photos
                                </span>
                                <span
                                    v-if="album.location"
                                    class="flex items-center gap-1"
                                >
                                    <MapPin class="h-4 w-4" />
                                    {{ album.location }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- All Albums Grid -->
            <div>
                <h2 class="mb-6 text-2xl font-semibold">All Albums</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="album in albums"
                        :key="album.id"
                        :href="`/gallery/${album.slug}`"
                        class="group"
                    >
                        <Card
                            class="overflow-hidden transition-all hover:shadow-lg"
                        >
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img
                                    v-if="album.cover"
                                    :src="album.cover"
                                    :alt="album.title"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-muted"
                                >
                                    <Camera
                                        class="h-10 w-10 text-muted-foreground"
                                    />
                                </div>
                            </div>
                            <CardContent class="p-4">
                                <h3
                                    class="font-semibold group-hover:text-primary"
                                >
                                    {{ album.title }}
                                </h3>
                                <p
                                    v-if="album.summary"
                                    class="mt-1 line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{ album.summary }}
                                </p>
                                <div
                                    class="mt-3 flex items-center gap-3 text-xs text-muted-foreground"
                                >
                                    <span
                                        v-if="album.photo_count"
                                        class="flex items-center gap-1"
                                    >
                                        <Camera class="h-3 w-3" />
                                        {{ album.photo_count }}
                                    </span>
                                    <span
                                        v-if="album.location"
                                        class="flex items-center gap-1"
                                    >
                                        <MapPin class="h-3 w-3" />
                                        {{ album.location }}
                                    </span>
                                    <span v-if="album.taken_at">{{
                                        album.taken_at
                                    }}</span>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!albums.length" class="py-20 text-center">
                <Camera class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">No albums yet</p>
                <p class="mt-2 text-muted-foreground">
                    Check back later for photo galleries
                </p>
            </div>
        </div>
    </section>
</template>
