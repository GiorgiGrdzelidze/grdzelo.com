<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Code2 } from 'lucide-vue-next';
import { useLocalePath } from '@/composables/useLocalePath';
import { useT } from '@/composables/useTranslate';

interface Metric {
    label: string;
    value: string;
}

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    cover_image: string | null;
    tech_stack: string[] | null;
    year: number | null;
    is_featured: boolean;
    client_type: string | null;
    industry: string | null;
    metrics: Metric[] | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    projects: Project[];
}

defineProps<Props>();

const { t } = useT();
const localePath = useLocalePath();

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function firstMetric(metrics: Metric[] | null): Metric | null {
    if (!metrics || !metrics.length) {
        return null;
    }

    const m = metrics[0];

    if (!m || !m.label || !m.value) {
        return null;
    }

    return m;
}
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.work.eyebrow') }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('projects.title') }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('projects.lead') }}
            </p>
        </div>
    </section>

    <!-- ============ GRID ============ -->
    <section
        v-if="projects.length"
        class="border-t border-border px-6 pb-32 sm:px-8 sm:pb-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div
                class="grid grid-cols-1 gap-px border-b border-border bg-border sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="(project, i) in projects"
                    :key="project.id"
                    :href="localePath(`/projects/${project.slug}`)"
                    class="group flex flex-col bg-background p-8 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>{{ pad(i + 1) }}</span>
                        <span v-if="project.year">{{ project.year }}</span>
                    </div>
                    <div
                        class="mt-6 flex aspect-[4/3] items-center justify-center overflow-hidden bg-muted"
                    >
                        <img
                            v-if="project.cover_image"
                            :src="`/storage/${project.cover_image}`"
                            :alt="project.title"
                            class="h-full w-full object-cover"
                        />
                        <Code2
                            v-else
                            class="h-8 w-8 text-muted-foreground"
                            :stroke-width="1.5"
                        />
                    </div>
                    <h2
                        class="mt-6 flex items-center gap-1 text-lg font-semibold tracking-[-0.01em]"
                    >
                        {{ project.title }}
                        <ArrowUpRight
                            class="h-3.5 w-3.5 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                        />
                    </h2>
                    <p
                        class="mt-3 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ project.summary }}
                    </p>

                    <!-- Key metric (first metric, if present) -->
                    <div
                        v-if="firstMetric(project.metrics)"
                        class="mt-6 border-t border-border pt-4"
                    >
                        <div
                            class="font-mono text-[10px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ firstMetric(project.metrics)!.label }}
                        </div>
                        <div
                            class="mt-1 font-mono text-xl font-semibold tracking-[-0.01em] text-foreground"
                        >
                            {{ firstMetric(project.metrics)!.value }}
                        </div>
                    </div>

                    <div
                        v-if="project.tech_stack?.length"
                        class="mt-6 flex flex-wrap gap-1.5"
                    >
                        <span
                            v-for="tech in project.tech_stack.slice(0, 5)"
                            :key="tech"
                            class="border border-border px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                        >
                            {{ tech }}
                        </span>
                        <span
                            v-if="project.tech_stack.length > 5"
                            class="border border-border px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                        >
                            +{{ project.tech_stack.length - 5 }}
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
            <Code2
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                {{ t('repositories.empty') }}
            </p>
        </div>
    </section>
</template>
