<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowUpRight } from 'lucide-vue-next';
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
    description: string;
    challenge: string | null;
    solution: string | null;
    process: string | null;
    tech_stack: string[] | null;
    role: string | null;
    client_type: string | null;
    industry: string | null;
    year: number | null;
    date_start: string | null;
    date_end: string | null;
    cover_image: string | null;
    gallery: string[] | null;
    live_url: string | null;
    repo_url: string | null;
    metrics: Metric[] | null;
    skills: Array<{ id: number; name: string; slug: string }>;
    testimonials: Array<{
        id: number;
        author_name: string;
        author_role: string | null;
        company: string | null;
        quote: string;
        avatar: string | null;
        rating: number | null;
    }>;
    tags: Array<{ id: number; name: string }>;
}

interface RelatedProject {
    id: number;
    title: string;
    slug: string;
    summary: string;
    cover_image: string | null;
    tech_stack: string[] | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    project: Project;
    relatedProjects: RelatedProject[];
}

defineProps<Props>();

const { t } = useT();
const localePath = useLocalePath();

function pad(n: number): string {
    return String(n).padStart(2, '0');
}
</script>

<template>
    <article>
        <!-- ============ HEADER ============ -->
        <section class="px-6 pt-16 pb-16 sm:px-8 sm:pt-24 sm:pb-20 lg:px-12">
            <div class="mx-auto max-w-[1200px]">
                <Link
                    href="/projects"
                    class="group inline-flex items-center gap-2 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    <ArrowLeft
                        class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5"
                    />
                    {{ t('actions.back_to_work') }}
                </Link>

                <div
                    class="mt-12 grid gap-12 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:items-end lg:gap-16"
                >
                    <div>
                        <span class="eyebrow">{{
                            t('sections.work.eyebrow')
                        }}</span>
                        <h1
                            class="mt-6 text-[clamp(2.25rem,5vw,3.75rem)] leading-[1.05] font-semibold tracking-[-0.025em] text-balance"
                        >
                            {{ project.title
                            }}<span class="text-accent">.</span>
                        </h1>
                        <p
                            v-if="project.summary"
                            class="mt-8 max-w-[60ch] text-lg leading-relaxed text-pretty text-muted-foreground"
                        >
                            {{ project.summary }}
                        </p>
                    </div>

                    <dl
                        class="grid grid-cols-2 gap-x-6 gap-y-6 border-t border-border pt-6 sm:grid-cols-3 lg:grid-cols-2 lg:border-t-0 lg:pt-0"
                    >
                        <div v-if="project.role">
                            <dt
                                class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ t('project.role') }}
                            </dt>
                            <dd class="mt-2 text-sm text-foreground">
                                {{ project.role }}
                            </dd>
                        </div>
                        <div v-if="project.client_type">
                            <dt
                                class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ t('project.client') }}
                            </dt>
                            <dd class="mt-2 text-sm text-foreground">
                                {{ project.client_type }}
                            </dd>
                        </div>
                        <div v-if="project.year">
                            <dt
                                class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ t('project.year') }}
                            </dt>
                            <dd class="mt-2 font-mono text-sm text-foreground">
                                {{ project.year }}
                            </dd>
                        </div>
                        <div v-if="project.industry">
                            <dt
                                class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                Industry
                            </dt>
                            <dd class="mt-2 text-sm text-foreground">
                                {{ project.industry }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div
                    v-if="project.live_url || project.repo_url"
                    class="mt-12 flex flex-wrap gap-4"
                >
                    <a
                        v-if="project.live_url"
                        :href="project.live_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group inline-flex items-center gap-2 bg-foreground px-5 py-3 text-sm font-medium text-background transition-opacity hover:opacity-90"
                    >
                        {{ t('project.visit_live') }}
                        <ArrowUpRight
                            class="h-4 w-4 text-accent transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                        />
                    </a>
                    <a
                        v-if="project.repo_url"
                        :href="project.repo_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 border border-border bg-transparent px-5 py-3 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                    >
                        {{ t('project.view_source') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- ============ COVER ============ -->
        <section
            v-if="project.cover_image"
            class="px-6 pb-24 sm:px-8 sm:pb-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <div
                    class="aspect-[16/9] w-full overflow-hidden border border-border bg-muted"
                >
                    <img
                        :src="`/storage/${project.cover_image}`"
                        :alt="project.title"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
        </section>

        <!-- ============ DESCRIPTION ============ -->
        <section
            v-if="project.description"
            class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <div
                    class="prose max-w-[70ch] text-pretty prose-neutral dark:prose-invert"
                    v-html="project.description"
                />
            </div>
        </section>

        <!-- ============ CASE STUDY: CHALLENGE / SOLUTION / PROCESS ============ -->
        <section
            v-if="project.challenge || project.solution || project.process"
            class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px] space-y-16">
                <div
                    v-if="project.challenge"
                    class="grid gap-8 md:grid-cols-[180px_1fr]"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / 01 · Challenge
                    </div>
                    <div
                        class="prose max-w-[65ch] text-pretty prose-neutral dark:prose-invert"
                        v-html="project.challenge"
                    />
                </div>
                <div
                    v-if="project.solution"
                    class="grid gap-8 md:grid-cols-[180px_1fr]"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / 02 · Solution
                    </div>
                    <div
                        class="prose max-w-[65ch] text-pretty prose-neutral dark:prose-invert"
                        v-html="project.solution"
                    />
                </div>
                <div
                    v-if="project.process"
                    class="grid gap-8 md:grid-cols-[180px_1fr]"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / 03 · Process
                    </div>
                    <div
                        class="prose max-w-[65ch] text-pretty prose-neutral dark:prose-invert"
                        v-html="project.process"
                    />
                </div>
            </div>
        </section>

        <!-- ============ METRICS ============ -->
        <section
            v-if="project.metrics?.length"
            class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <span class="eyebrow">Numbers</span>
                <div
                    class="mt-12 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="metric in project.metrics"
                        :key="metric.label"
                        class="bg-background p-8"
                    >
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ metric.label }}
                        </div>
                        <div
                            class="mt-3 font-mono text-3xl font-semibold tracking-[-0.01em] text-foreground"
                        >
                            {{ metric.value }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ TECH STACK ============ -->
        <section
            v-if="project.tech_stack?.length || project.skills?.length"
            class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px] space-y-12">
                <div v-if="project.tech_stack?.length">
                    <span class="eyebrow">{{ t('project.stack') }}</span>
                    <div class="mt-6 flex flex-wrap gap-1.5">
                        <span
                            v-for="tech in project.tech_stack"
                            :key="tech"
                            class="border border-border px-3 py-1.5 font-mono text-xs text-foreground"
                        >
                            {{ tech }}
                        </span>
                    </div>
                </div>
                <div v-if="project.skills?.length">
                    <span class="eyebrow">{{
                        t('sections.skills.eyebrow')
                    }}</span>
                    <div class="mt-6 flex flex-wrap gap-1.5">
                        <span
                            v-for="skill in project.skills"
                            :key="skill.id"
                            class="border border-border px-3 py-1.5 font-mono text-xs text-foreground"
                        >
                            {{ skill.name }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ TESTIMONIALS ============ -->
        <section
            v-if="project.testimonials?.length"
            class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <span class="eyebrow">{{
                    t('sections.feedback.eyebrow')
                }}</span>
                <div
                    class="mt-12 grid grid-cols-1 gap-px bg-border md:grid-cols-2"
                >
                    <figure
                        v-for="testimonial in project.testimonials"
                        :key="testimonial.id"
                        class="flex flex-col justify-between bg-background p-8"
                    >
                        <blockquote
                            class="text-base leading-relaxed text-pretty text-foreground"
                        >
                            “{{ testimonial.quote }}”
                        </blockquote>
                        <figcaption
                            class="mt-8 border-t border-border pt-6 font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                        >
                            <div class="text-foreground">
                                {{ testimonial.author_name }}
                            </div>
                            <div
                                v-if="
                                    testimonial.author_role ||
                                    testimonial.company
                                "
                                class="mt-1"
                            >
                                {{
                                    [
                                        testimonial.author_role,
                                        testimonial.company,
                                    ]
                                        .filter(Boolean)
                                        .join(' · ')
                                }}
                            </div>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>

        <!-- ============ GALLERY ============ -->
        <section
            v-if="project.gallery?.length"
            class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <span class="eyebrow">{{ t('project.gallery') }}</span>
                <div
                    class="mt-12 grid grid-cols-1 gap-px bg-border sm:grid-cols-2"
                >
                    <div
                        v-for="(img, idx) in project.gallery"
                        :key="idx"
                        class="aspect-[4/3] overflow-hidden bg-muted"
                    >
                        <img
                            :src="`/storage/${img}`"
                            :alt="`${project.title} — ${idx + 1}`"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ RELATED ============ -->
        <section
            v-if="relatedProjects.length"
            class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
        >
            <div class="mx-auto max-w-[1200px]">
                <div class="mb-16 flex items-end justify-between gap-4">
                    <div>
                        <span class="eyebrow">{{ t('project.related') }}</span>
                        <h2
                            class="mt-4 text-[clamp(1.5rem,2.5vw,2rem)] font-semibold tracking-[-0.02em]"
                        >
                            {{ t('project.related') }}
                        </h2>
                    </div>
                    <Link
                        href="/projects"
                        class="group inline-flex shrink-0 items-center gap-1 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                    >
                        {{ t('actions.view_all') }}
                        <ArrowUpRight
                            class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                        />
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-px bg-border sm:grid-cols-3">
                    <Link
                        v-for="(rp, i) in relatedProjects"
                        :key="rp.id"
                        :href="localePath(`/projects/${rp.slug}`)"
                        class="group flex flex-col bg-background p-8 transition-colors hover:bg-muted/30"
                    >
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ pad(i + 1) }}
                        </div>
                        <h3
                            class="mt-6 flex items-center gap-1 text-base font-semibold tracking-[-0.01em]"
                        >
                            {{ rp.title }}
                            <ArrowUpRight
                                class="h-3.5 w-3.5 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            />
                        </h3>
                        <p
                            class="mt-3 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                        >
                            {{ rp.summary }}
                        </p>
                    </Link>
                </div>
            </div>
        </section>
    </article>
</template>
