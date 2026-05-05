<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Bike,
    Book,
    Camera,
    Compass,
    Crosshair,
    Flag,
    Globe,
    Heart,
    Mountain,
    Moon,
    Music,
    Paintbrush,
    Tent,
    Truck,
    Zap,
} from 'lucide-vue-next';
import type { Component } from 'vue';
import { useLocalePath } from '@/composables/useLocalePath';
import { useT } from '@/composables/useTranslate';

interface HobbyItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    description: string | null;
    cover: string | null;
    icon: string | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    hobbies: HobbyItem[];
}

defineProps<Props>();

const { t } = useT();
const localePath = useLocalePath();

const ICON_MAP: Record<string, Component> = {
    'heroicon-o-crosshair': Crosshair,
    'heroicon-o-globe-alt': Globe,
    'heroicon-o-globe': Globe,
    'heroicon-o-truck': Truck,
    'heroicon-o-bolt': Zap,
    'heroicon-o-flag': Flag,
    'heroicon-o-moon': Moon,
    'heroicon-o-camera': Camera,
    'heroicon-o-musical-note': Music,
    'heroicon-o-book-open': Book,
    'heroicon-o-paint-brush': Paintbrush,
    'heroicon-o-map': Compass,
    'heroicon-o-fire': Tent,
    'heroicon-o-heart': Heart,
};

function resolveIcon(icon: string | null): Component {
    if (!icon) {
        return Mountain;
    }

    if (ICON_MAP[icon]) {
        return ICON_MAP[icon];
    }

    const lower = icon.toLowerCase();

    if (lower.includes('mountain')) {
        return Mountain;
    }

    if (lower.includes('bike') || lower.includes('cycle')) {
        return Bike;
    }

    if (lower.includes('camera')) {
        return Camera;
    }

    if (lower.includes('music')) {
        return Music;
    }

    if (lower.includes('book')) {
        return Book;
    }

    return Mountain;
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
}
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.hobbies.eyebrow') }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('hobbies.title') || 'Outside the editor'
                }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{
                    t('hobbies.lead') ||
                    "What I get up to when I close the laptop — usually involves altitude, water, or going somewhere a road doesn't."
                }}
            </p>
        </div>
    </section>

    <!-- ============ GRID ============ -->
    <section
        v-if="hobbies.length"
        class="border-t border-border px-6 pb-32 sm:px-8 sm:pb-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div
                class="grid grid-cols-1 gap-px border-b border-border bg-border sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="(hobby, i) in hobbies"
                    :key="hobby.id"
                    :href="localePath(`/hobbies/${hobby.slug}`)"
                    class="group flex flex-col bg-background p-8 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                    </div>

                    <div
                        class="mt-6 flex aspect-[4/3] items-center justify-center overflow-hidden border border-border bg-muted/40"
                    >
                        <img
                            v-if="hobby.cover"
                            :src="hobby.cover"
                            :alt="hobby.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                        <component
                            :is="resolveIcon(hobby.icon)"
                            v-else
                            class="h-12 w-12 text-muted-foreground transition-colors group-hover:text-accent"
                            :stroke-width="1.5"
                            aria-hidden="true"
                        />
                    </div>

                    <h2
                        class="mt-6 inline-flex items-center gap-1 text-lg font-semibold tracking-[-0.01em]"
                    >
                        {{ hobby.title }}
                        <ArrowUpRight
                            class="h-3.5 w-3.5 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            aria-hidden="true"
                        />
                    </h2>

                    <p
                        v-if="hobby.summary"
                        class="mt-3 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ hobby.summary }}
                    </p>
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
            <Heart
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
                aria-hidden="true"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                Hobbies coming soon
            </p>
        </div>
    </section>
</template>
