<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, GitFork, Github, Star } from 'lucide-vue-next';
import { useLocalePath } from '@/composables/useLocalePath';
import { useT } from '@/composables/useTranslate';

interface RepositoryItem {
    id: number;
    name: string;
    slug: string;
    url: string;
    summary: string | null;
    owner: string | null;
    language: string | null;
    technologies: string[] | null;
    stars: number;
    forks: number;
    status: string;
    is_featured: boolean;
    demo_url: string | null;
    cover: string | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    repositories: RepositoryItem[];
    featured: RepositoryItem[];
}

defineProps<Props>();

const { t } = useT();
const localePath = useLocalePath();

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ');
}

function isActive(status: string): boolean {
    return status === 'active';
}
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{
                t('sections.repositories.eyebrow')
            }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('repositories.title') }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('repositories.lead') }}
            </p>
        </div>
    </section>

    <!-- ============ FEATURED ============ -->
    <section
        v-if="featured.length"
        class="border-t border-border px-6 pb-16 sm:px-8 sm:pb-20 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-6 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    Featured
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(featured.length) }} / {{ pad(repositories.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-1 gap-px border border-border bg-border md:grid-cols-2"
            >
                <article
                    v-for="(repo, i) in featured"
                    :key="repo.id"
                    class="group relative flex flex-col bg-background p-8 transition-colors hover:bg-muted/30 sm:p-10"
                >
                    <Link
                        :href="localePath(`/repositories/${repo.slug}`)"
                        class="absolute inset-0 z-0"
                        :aria-label="`View ${repo.name} details`"
                    />
                    <div
                        class="pointer-events-none relative z-10 flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                        <span
                            class="inline-flex items-center gap-1.5"
                            :class="
                                isActive(repo.status)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                        >
                            <span
                                v-if="isActive(repo.status)"
                                class="status-dot"
                                aria-hidden="true"
                            />
                            {{ formatStatus(repo.status) }}
                        </span>
                    </div>

                    <h2
                        class="pointer-events-none relative z-10 mt-8 inline-flex items-center gap-1 text-2xl font-semibold tracking-[-0.02em] text-foreground"
                    >
                        {{ repo.name }}
                        <ArrowUpRight
                            class="h-4 w-4 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            aria-hidden="true"
                        />
                    </h2>
                    <div
                        v-if="repo.owner"
                        class="pointer-events-none relative z-10 mt-2 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ repo.owner }}
                    </div>

                    <p
                        v-if="repo.summary"
                        class="pointer-events-none relative z-10 mt-6 max-w-[55ch] text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ repo.summary }}
                    </p>

                    <div
                        v-if="repo.technologies?.length"
                        class="pointer-events-none relative z-10 mt-6 flex flex-wrap gap-1.5"
                    >
                        <span
                            v-for="tech in repo.technologies.slice(0, 6)"
                            :key="tech"
                            class="border border-border px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                        >
                            {{ tech }}
                        </span>
                    </div>

                    <div
                        class="pointer-events-none relative z-10 mt-auto flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-border pt-6"
                    >
                        <span
                            v-if="repo.language"
                            class="font-mono text-[11px] tracking-[0.12em] text-foreground uppercase"
                        >
                            {{ repo.language }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.08em] text-muted-foreground"
                        >
                            <Star class="h-3.5 w-3.5" aria-hidden="true" />
                            {{ repo.stars }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.08em] text-muted-foreground"
                        >
                            <GitFork class="h-3.5 w-3.5" aria-hidden="true" />
                            {{ repo.forks }}
                        </span>

                        <div
                            class="pointer-events-auto ml-auto flex items-center gap-5 font-mono text-[11px] font-medium tracking-[0.12em] uppercase"
                        >
                            <a
                                :href="repo.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group/cta relative z-10 inline-flex items-center gap-1.5 text-foreground transition-colors hover:text-accent"
                                :aria-label="`View ${repo.name} on GitHub`"
                                @click.stop
                            >
                                <Github
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
                                />
                                GitHub
                            </a>
                            <a
                                v-if="repo.demo_url"
                                :href="repo.demo_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group/cta relative z-10 inline-flex items-center gap-1 text-foreground transition-colors hover:text-accent"
                                :aria-label="`Open ${repo.name} live demo`"
                                @click.stop
                            >
                                Demo
                                <ArrowUpRight
                                    class="h-3.5 w-3.5 text-accent"
                                    aria-hidden="true"
                                />
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============ ALL ============ -->
    <section
        v-if="repositories.length"
        class="border-t border-border px-6 pt-16 pb-24 sm:px-8 sm:pt-20 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-4 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    All repositories
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(repositories.length) }}
                </span>
            </div>

            <ul class="border-t border-border">
                <li
                    v-for="(repo, i) in repositories"
                    :key="repo.id"
                    class="group/row relative grid grid-cols-1 items-start gap-2 border-b border-border py-4 transition-colors hover:bg-muted/30 sm:grid-cols-[32px_minmax(0,1.4fr)_minmax(0,1fr)_auto] sm:items-center sm:gap-6 sm:py-5"
                >
                    <Link
                        :href="localePath(`/repositories/${repo.slug}`)"
                        class="absolute inset-0 z-0"
                        :aria-label="`View ${repo.name} details`"
                    />
                    <span
                        class="pointer-events-none relative z-10 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ pad(i + 1) }}
                    </span>

                    <div class="pointer-events-none relative z-10 min-w-0">
                        <div
                            class="inline-flex items-center gap-1 text-base font-semibold tracking-[-0.01em] text-foreground"
                        >
                            {{ repo.name }}
                            <ArrowUpRight
                                class="h-3.5 w-3.5 text-muted-foreground transition-all group-hover/row:translate-x-0.5 group-hover/row:-translate-y-0.5 group-hover/row:text-accent"
                                aria-hidden="true"
                            />
                        </div>
                        <p
                            v-if="repo.summary"
                            class="mt-1 line-clamp-2 max-w-[60ch] text-sm leading-relaxed text-pretty text-muted-foreground"
                        >
                            {{ repo.summary }}
                        </p>
                    </div>

                    <div
                        class="pointer-events-none relative z-10 flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                    >
                        <span v-if="repo.language" class="text-foreground">{{
                            repo.language
                        }}</span>
                        <span class="inline-flex items-center gap-1.5">
                            <Star class="h-3 w-3" aria-hidden="true" />
                            {{ repo.stars }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <GitFork class="h-3 w-3" aria-hidden="true" />
                            {{ repo.forks }}
                        </span>
                        <span
                            v-if="!isActive(repo.status)"
                            class="text-muted-foreground"
                        >
                            {{ formatStatus(repo.status) }}
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center gap-1.5 text-foreground"
                        >
                            <span class="status-dot" aria-hidden="true" />
                            active
                        </span>
                    </div>

                    <a
                        :href="repo.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="`View ${repo.name} on GitHub`"
                        class="relative z-10 inline-flex items-center gap-1.5 justify-self-start font-mono text-[11px] font-medium tracking-[0.12em] text-foreground uppercase transition-colors hover:text-accent sm:justify-self-end"
                        @click.stop
                    >
                        <Github class="h-3.5 w-3.5" aria-hidden="true" />
                        GitHub
                    </a>
                </li>
            </ul>
        </div>
    </section>

    <!-- ============ EMPTY STATE ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <Github
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
                aria-hidden="true"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                {{ t('repositories.empty') }}
            </p>
        </div>
    </section>
</template>
