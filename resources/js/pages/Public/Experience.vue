<script setup lang="ts">
import { ArrowUpRight, Briefcase } from 'lucide-vue-next';
import { computed } from 'vue';
import { useT } from '@/composables/useTranslate';

interface ExperienceItem {
    id: number;
    company: string;
    role: string;
    type: string;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    summary: string | null;
    achievements: string[] | null;
    technologies: string[] | null;
    website_url: string | null;
    logo: string | null;
}

interface Props {
    settings?: Record<string, any>;
    seo?: Record<string, any>;
    experiences: ExperienceItem[];
}

const props = defineProps<Props>();

const { t, locale } = useT();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString(locale.value || 'en-US', {
        year: 'numeric',
        month: 'short',
    });
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

const totalYears = computed(() => {
    if (!props.experiences.length) {
        return null;
    }

    const earliest = props.experiences.reduce<Date | null>((acc, exp) => {
        const d = new Date(exp.start_date);

        return !acc || d < acc ? d : acc;
    }, null);

    if (!earliest) {
        return null;
    }

    const now = new Date();
    const years =
        (now.getTime() - earliest.getTime()) / (365.25 * 24 * 3600 * 1000);

    return Math.max(1, Math.round(years));
});
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{
                t('sections.experience.eyebrow') || 'Track record'
            }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('experience.title') || 'Experience'
                }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{
                    t('experience.lead') ||
                    'A professional journey through roles, products, and the teams that shaped how I build software.'
                }}
            </p>

            <div
                v-if="experiences.length"
                class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-border pt-6 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
            >
                <span>{{ pad(experiences.length) }} positions</span>
                <span v-if="totalYears">{{ pad(totalYears) }}+ years</span>
            </div>
        </div>
    </section>

    <!-- ============ TIMELINE ============ -->
    <section
        v-if="experiences.length"
        class="border-t border-border px-6 pt-12 pb-24 sm:px-8 sm:pt-16 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1100px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('experience.timeline') || 'Timeline' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(experiences.length) }}
                </span>
            </div>

            <ol class="border-t border-border">
                <li
                    v-for="(exp, i) in experiences"
                    :key="exp.id"
                    class="border-b border-border py-8 sm:py-10"
                >
                    <div
                        class="grid gap-6 sm:grid-cols-[120px_1fr] sm:gap-10 lg:grid-cols-[160px_1fr]"
                    >
                        <!-- Left rail: index + dates -->
                        <div class="flex flex-col gap-3">
                            <div
                                class="flex items-center gap-2 font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                <span>/ {{ pad(i + 1) }}</span>
                                <span
                                    v-if="exp.is_current"
                                    class="inline-flex items-center gap-1.5 text-foreground"
                                >
                                    <span
                                        class="status-dot"
                                        aria-hidden="true"
                                    />
                                    {{ t('experience.current') || 'Current' }}
                                </span>
                            </div>
                            <div
                                class="font-mono text-[11px] tracking-[0.12em] text-foreground uppercase"
                            >
                                {{ formatDate(exp.start_date) }}
                                <span class="text-muted-foreground">—</span>
                                {{
                                    exp.is_current
                                        ? t('experience.present') || 'Present'
                                        : exp.end_date
                                          ? formatDate(exp.end_date)
                                          : ''
                                }}
                            </div>
                            <div
                                v-if="exp.type"
                                class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ exp.type }}
                            </div>
                        </div>

                        <!-- Right: role + company + body -->
                        <div class="min-w-0">
                            <h2
                                class="text-xl font-semibold tracking-[-0.02em] text-foreground sm:text-2xl"
                            >
                                {{ exp.role }}<span class="text-accent">.</span>
                            </h2>

                            <div
                                class="mt-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                <Briefcase
                                    class="h-3 w-3 shrink-0"
                                    aria-hidden="true"
                                />
                                <a
                                    v-if="exp.website_url"
                                    :href="exp.website_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group inline-flex items-center gap-1 text-foreground transition-colors hover:text-accent"
                                >
                                    {{ exp.company }}
                                    <ArrowUpRight
                                        class="h-3 w-3 text-muted-foreground transition-colors group-hover:text-accent"
                                        aria-hidden="true"
                                    />
                                </a>
                                <span v-else class="text-foreground">{{
                                    exp.company
                                }}</span>
                            </div>

                            <p
                                v-if="exp.summary"
                                class="mt-5 max-w-[70ch] text-base leading-relaxed text-pretty text-muted-foreground"
                            >
                                {{ exp.summary }}
                            </p>

                            <ul
                                v-if="exp.achievements?.length"
                                class="mt-5 space-y-2 border-l border-border pl-5"
                            >
                                <li
                                    v-for="(a, idx) in exp.achievements"
                                    :key="idx"
                                    class="flex gap-3 text-sm leading-relaxed text-pretty text-foreground"
                                >
                                    <span
                                        class="mt-2 h-px w-3 shrink-0 bg-accent"
                                        aria-hidden="true"
                                    />
                                    <span>{{ a }}</span>
                                </li>
                            </ul>

                            <div
                                v-if="exp.technologies?.length"
                                class="mt-6 flex flex-wrap gap-1.5"
                            >
                                <span
                                    v-for="tech in exp.technologies"
                                    :key="tech"
                                    class="border border-border bg-background px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                                >
                                    {{ tech }}
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <!-- ============ EMPTY ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <p
                class="font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                {{ t('experience.empty') || 'Experience details coming soon' }}
            </p>
        </div>
    </section>
</template>
