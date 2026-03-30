<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Camera, MapPin, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';

interface Photo {
    id: number;
    url: string;
    thumb: string;
    preview: string;
    alt: string;
    caption: string | null;
}

interface AlbumDetail {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    description: string | null;
    cover: string | null;
    location: string | null;
    taken_at: string | null;
}

interface Props {
    album: AlbumDetail;
    photos: Photo[];
}

defineProps<Props>();

const lightboxOpen = ref(false);
const currentPhotoIndex = ref(0);

function openLightbox(index: number) {
    currentPhotoIndex.value = index;
    lightboxOpen.value = true;
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightboxOpen.value = false;
    document.body.style.overflow = '';
}

function nextPhoto(photos: Photo[]) {
    currentPhotoIndex.value = (currentPhotoIndex.value + 1) % photos.length;
}

function prevPhoto(photos: Photo[]) {
    currentPhotoIndex.value = (currentPhotoIndex.value - 1 + photos.length) % photos.length;
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-8">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/gallery">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Gallery
                    </Link>
                </Button>
            </div>

            <!-- Album Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold tracking-tight">{{ album.title }}</h1>
                <div v-if="album.location || album.taken_at" class="mt-4 flex items-center gap-4 text-muted-foreground">
                    <span v-if="album.location" class="flex items-center gap-1">
                        <MapPin class="h-4 w-4" />
                        {{ album.location }}
                    </span>
                    <span v-if="album.taken_at">{{ album.taken_at }}</span>
                </div>
                <p v-if="album.summary" class="mt-4 text-lg text-muted-foreground">
                    {{ album.summary }}
                </p>
                <div v-if="album.description" class="prose prose-neutral mt-6 max-w-none dark:prose-invert" v-html="album.description" />
            </div>

            <!-- Photo Grid -->
            <div v-if="photos.length" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <button
                    v-for="(photo, index) in photos"
                    :key="photo.id"
                    class="group relative aspect-square overflow-hidden rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    @click="openLightbox(index)"
                >
                    <img
                        :src="photo.thumb"
                        :alt="photo.alt"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20" />
                </button>
            </div>

            <!-- Empty State -->
            <div v-else class="py-20 text-center">
                <Camera class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">No photos in this album yet</p>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="lightboxOpen && photos.length"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/95"
                @click.self="closeLightbox"
                @keydown.escape="closeLightbox"
                @keydown.left="prevPhoto(photos)"
                @keydown.right="nextPhoto(photos)"
            >
                <!-- Close Button -->
                <button
                    class="absolute right-4 top-4 rounded-full p-2 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                    @click="closeLightbox"
                >
                    <X class="h-6 w-6" />
                </button>

                <!-- Navigation -->
                <button
                    v-if="photos.length > 1"
                    class="absolute left-4 rounded-full p-3 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                    @click="prevPhoto(photos)"
                >
                    <ArrowLeft class="h-6 w-6" />
                </button>

                <!-- Image -->
                <div class="max-h-[90vh] max-w-[90vw]">
                    <img
                        :src="photos[currentPhotoIndex].url"
                        :alt="photos[currentPhotoIndex].alt"
                        class="max-h-[90vh] max-w-[90vw] object-contain"
                    />
                    <p v-if="photos[currentPhotoIndex].caption" class="mt-4 text-center text-white/80">
                        {{ photos[currentPhotoIndex].caption }}
                    </p>
                </div>

                <button
                    v-if="photos.length > 1"
                    class="absolute right-4 rounded-full p-3 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                    @click="nextPhoto(photos)"
                >
                    <ArrowLeft class="h-6 w-6 rotate-180" />
                </button>

                <!-- Counter -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-sm text-white/60">
                    {{ currentPhotoIndex + 1 }} / {{ photos.length }}
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
