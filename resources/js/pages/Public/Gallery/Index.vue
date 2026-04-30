<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Camera, MapPin } from 'lucide-vue-next';
import { useT } from '@/composables/useTranslate';

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
    settings: Record<string, any>;
    seo: Record<string, any>;
    albums: AlbumItem[];
    featured: AlbumItem[];
}

defineProps<Props>();

const { t } = useT();

function pad(n: number): string {
    return String(n).padStart(2, '0');
}
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{
                t('sections.gallery.eyebrow') || 'Field notes — visual'
            }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('gallery.title') || 'Gallery'
                }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{
                    t('gallery.lead') ||
                    'Photographs from the trail and the road — Caucasus mountain ranges, Tbilisi backstreets, occasional detours.'
                }}
            </p>
        </div>
    </section>

    <!-- ============ FEATURED ============ -->
    <section
        v-if="featured.length"
        class="border-t border-border px-6 pt-8 pb-10 sm:px-8 sm:pt-10 sm:pb-12 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('gallery.featured') || 'Featured' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(featured.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-1 gap-px border border-border bg-border md:grid-cols-2"
            >
                <Link
                    v-for="(album, i) in featured"
                    :key="album.id"
                    :href="`/gallery/${album.slug}`"
                    class="group relative flex aspect-[4/3] flex-col justify-end overflow-hidden bg-background"
                >
                    <img
                        v-if="album.cover"
                        :src="album.cover"
                        :alt="album.title"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                        loading="lazy"
                    />
                    <div
                        v-else
                        class="absolute inset-0 flex items-center justify-center bg-muted/40"
                    >
                        <Camera
                            class="h-10 w-10 text-muted-foreground"
                            :stroke-width="1.5"
                            aria-hidden="true"
                        />
                    </div>

                    <div
                        v-if="album.cover"
                        class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"
                        aria-hidden="true"
                    />

                    <div class="relative p-6 sm:p-8">
                        <div
                            class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] uppercase"
                            :class="
                                album.cover
                                    ? 'text-white/80'
                                    : 'text-muted-foreground'
                            "
                        >
                            <span>/ {{ pad(i + 1) }}</span>
                            <span v-if="album.photo_count">
                                {{ pad(album.photo_count) }} frames
                            </span>
                        </div>

                        <h2
                            class="mt-6 inline-flex items-center gap-1 text-2xl font-semibold tracking-[-0.02em]"
                            :class="
                                album.cover ? 'text-white' : 'text-foreground'
                            "
                        >
                            {{ album.title }}
                            <ArrowUpRight
                                class="h-4 w-4 transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                                :class="
                                    album.cover
                                        ? 'text-white/70'
                                        : 'text-muted-foreground'
                                "
                                aria-hidden="true"
                            />
                        </h2>

                        <div
                            v-if="album.location || album.taken_at"
                            class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px] tracking-[0.12em] uppercase"
                            :class="
                                album.cover
                                    ? 'text-white/70'
                                    : 'text-muted-foreground'
                            "
                        >
                            <span
                                v-if="album.location"
                                class="inline-flex items-center gap-1.5"
                            >
                                <MapPin class="h-3 w-3" aria-hidden="true" />
                                {{ album.location }}
                            </span>
                            <span v-if="album.taken_at">
                                {{ album.taken_at }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <!-- ============ ALL ALBUMS ============ -->
    <section
        v-if="albums.length"
        class="border-t border-border px-6 pt-12 pb-24 sm:px-8 sm:pt-16 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('gallery.all') || 'All albums' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(albums.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-1 gap-px border border-border bg-border sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="(album, i) in albums"
                    :key="album.id"
                    :href="`/gallery/${album.slug}`"
                    class="group flex flex-col bg-background p-5 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                        <span v-if="album.photo_count">
                            {{ pad(album.photo_count) }}
                        </span>
                    </div>

                    <div
                        class="mt-3 aspect-[4/3] overflow-hidden border border-border bg-muted/40"
                    >
                        <img
                            v-if="album.cover"
                            :src="album.cover"
                            :alt="album.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <Camera
                                class="h-9 w-9 text-muted-foreground transition-colors group-hover:text-accent"
                                :stroke-width="1.5"
                                aria-hidden="true"
                            />
                        </div>
                    </div>

                    <h3
                        class="mt-3 inline-flex items-start gap-1 text-base font-semibold tracking-[-0.01em] text-foreground sm:text-lg"
                    >
                        {{ album.title }}
                        <ArrowUpRight
                            class="mt-1 h-3.5 w-3.5 shrink-0 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            aria-hidden="true"
                        />
                    </h3>

                    <p
                        v-if="album.summary"
                        class="mt-2 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ album.summary }}
                    </p>

                    <div
                        v-if="album.location || album.taken_at"
                        class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-1 pt-3 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span
                            v-if="album.location"
                            class="inline-flex items-center gap-1.5"
                        >
                            <MapPin class="h-3 w-3" aria-hidden="true" />
                            {{ album.location }}
                        </span>
                        <span v-if="album.taken_at">
                            {{ album.taken_at }}
                        </span>
                    </div>
                </Link>
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
                {{ t('gallery.empty') || 'No albums yet' }}
            </p>
        </div>
    </section>
</template>
