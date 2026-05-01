<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Camera, MapPin, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useT } from '@/composables/useTranslate';

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
    settings: Record<string, any>;
    seo: Record<string, any>;
    album: AlbumDetail;
    photos: Photo[];
}

const props = defineProps<Props>();

const { t } = useT();

const lightboxOpen = ref(false);
const currentPhotoIndex = ref(0);

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function openLightbox(index: number): void {
    currentPhotoIndex.value = index;
    lightboxOpen.value = true;
    document.body.style.overflow = 'hidden';
}

function closeLightbox(): void {
    lightboxOpen.value = false;
    document.body.style.overflow = '';
}

function nextPhoto(): void {
    if (!props.photos.length) {
        return;
    }

    currentPhotoIndex.value =
        (currentPhotoIndex.value + 1) % props.photos.length;
}

function prevPhoto(): void {
    if (!props.photos.length) {
        return;
    }

    currentPhotoIndex.value =
        (currentPhotoIndex.value - 1 + props.photos.length) %
        props.photos.length;
}

function handleKey(e: KeyboardEvent): void {
    if (!lightboxOpen.value) {
        return;
    }

    if (e.key === 'Escape') {
        closeLightbox();
    }

    if (e.key === 'ArrowRight') {
        nextPhoto();
    }

    if (e.key === 'ArrowLeft') {
        prevPhoto();
    }
}

onMounted(() => window.addEventListener('keydown', handleKey));
onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKey);
    document.body.style.overflow = '';
});
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-20 pb-10 sm:px-8 sm:pt-28 sm:pb-12 lg:px-12">
        <div class="mx-auto max-w-[1100px]">
            <Link
                href="/gallery"
                class="group inline-flex items-center gap-1.5 font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
            >
                <ArrowLeft
                    class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5"
                    aria-hidden="true"
                />
                {{ t('gallery.back') || 'Back to gallery' }}
            </Link>

            <div class="mt-10">
                <h1
                    class="text-[clamp(2rem,4.5vw,3.25rem)] leading-[1.08] font-semibold tracking-[-0.025em] text-balance"
                >
                    {{ album.title }}<span class="text-accent">.</span>
                </h1>

                <p
                    v-if="album.summary"
                    class="mt-6 max-w-[60ch] text-lg leading-relaxed text-pretty text-muted-foreground"
                >
                    {{ album.summary }}
                </p>

                <div
                    v-if="album.location || album.taken_at || photos.length"
                    class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-border pt-6 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    <span
                        v-if="album.location"
                        class="inline-flex items-center gap-1.5 text-foreground"
                    >
                        <MapPin class="h-3.5 w-3.5" aria-hidden="true" />
                        {{ album.location }}
                    </span>
                    <span v-if="album.taken_at">{{ album.taken_at }}</span>
                    <span v-if="photos.length">
                        {{ pad(photos.length) }} frames
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ DESCRIPTION ============ -->
    <section
        v-if="album.description"
        class="border-t border-border px-6 pt-12 pb-12 sm:px-8 sm:pt-16 sm:pb-16 lg:px-12"
    >
        <div class="mx-auto max-w-[760px]">
            <div
                class="prose max-w-none prose-neutral dark:prose-invert prose-headings:font-semibold prose-headings:tracking-[-0.02em] prose-a:text-foreground prose-a:underline-offset-4 hover:prose-a:text-accent"
                v-html="album.description"
            />
        </div>
    </section>

    <!-- ============ PHOTO GRID ============ -->
    <section
        v-if="photos.length"
        class="border-t border-border px-6 pt-10 pb-24 sm:px-8 sm:pt-12 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1400px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('gallery.frames') || 'Frames' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(photos.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-2 gap-px border border-border bg-border sm:grid-cols-3 lg:grid-cols-4"
            >
                <button
                    v-for="(photo, index) in photos"
                    :key="photo.id"
                    type="button"
                    class="group relative aspect-square overflow-hidden bg-background focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                    :aria-label="`Open photo ${index + 1}`"
                    @click="openLightbox(index)"
                >
                    <img
                        :src="photo.thumb"
                        :alt="photo.alt"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                        loading="lazy"
                    />
                    <div
                        class="pointer-events-none absolute inset-0 flex items-end justify-between p-2 opacity-0 transition-opacity group-hover:opacity-100"
                        aria-hidden="true"
                    >
                        <span
                            class="bg-foreground/85 px-1.5 py-0.5 font-mono text-[10px] tracking-[0.12em] text-background uppercase"
                        >
                            {{ pad(index + 1) }}
                        </span>
                    </div>
                </button>
            </div>
        </div>
    </section>

    <!-- ============ EMPTY STATE ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <Camera
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
                aria-hidden="true"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                {{ t('gallery.empty_album') || 'No frames in this album yet' }}
            </p>
        </div>
    </section>

    <!-- ============ LIGHTBOX ============ -->
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
                role="dialog"
                aria-modal="true"
                :aria-label="`${album.title} — photo ${currentPhotoIndex + 1} of ${photos.length}`"
                @click.self="closeLightbox"
            >
                <button
                    type="button"
                    class="absolute top-4 right-4 inline-flex h-10 w-10 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/60 hover:text-white"
                    :aria-label="t('gallery.close') || 'Close'"
                    @click="closeLightbox"
                >
                    <X class="h-4 w-4" aria-hidden="true" />
                </button>

                <button
                    v-if="photos.length > 1"
                    type="button"
                    class="absolute left-4 inline-flex h-10 w-10 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/60 hover:text-white sm:left-8"
                    :aria-label="t('gallery.prev') || 'Previous photo'"
                    @click="prevPhoto"
                >
                    <ArrowLeft class="h-4 w-4" aria-hidden="true" />
                </button>

                <div
                    class="flex max-h-[90vh] max-w-[90vw] flex-col items-center justify-center"
                >
                    <img
                        :src="photos[currentPhotoIndex].url"
                        :alt="photos[currentPhotoIndex].alt"
                        class="max-h-[78vh] max-w-[90vw] border border-white/10 object-contain"
                    />
                    <p
                        v-if="photos[currentPhotoIndex].caption"
                        class="mt-4 max-w-[60ch] text-center text-sm leading-relaxed text-pretty text-white/75"
                    >
                        {{ photos[currentPhotoIndex].caption }}
                    </p>
                </div>

                <button
                    v-if="photos.length > 1"
                    type="button"
                    class="absolute right-4 inline-flex h-10 w-10 items-center justify-center border border-white/20 text-white/80 transition-colors hover:border-white/60 hover:text-white sm:right-8"
                    :aria-label="t('gallery.next') || 'Next photo'"
                    @click="nextPhoto"
                >
                    <ArrowRight class="h-4 w-4" aria-hidden="true" />
                </button>

                <div
                    class="absolute bottom-6 left-1/2 -translate-x-1/2 font-mono text-[11px] tracking-[0.12em] text-white/60 uppercase"
                    aria-hidden="true"
                >
                    {{ pad(currentPhotoIndex + 1) }} —
                    {{ pad(photos.length) }}
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
