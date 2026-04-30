<script setup lang="ts">
import { GraduationCap, MapPin } from 'lucide-vue-next';
import { useT } from '@/composables/useTranslate';

interface EducationItem {
    id: number;
    institution: string;
    degree: string | null;
    field_of_study: string | null;
    location: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
    achievements: string[] | null;
    logo: string | null;
    is_featured: boolean;
}

interface Props {
    settings?: Record<string, any>;
    seo?: Record<string, any>;
    education: EducationItem[];
}

defineProps<Props>();

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
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{
                t('sections.education.eyebrow') || 'Foundation'
            }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('education.title') || 'Education'
                }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{
                    t('education.lead') ||
                    'Academic background and qualifications that shape how I think about systems and software.'
                }}
            </p>

            <div
                v-if="education.length"
                class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-border pt-6 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
            >
                <span>{{ pad(education.length) }} entries</span>
            </div>
        </div>
    </section>

    <!-- ============ TIMELINE ============ -->
    <section
        v-if="education.length"
        class="border-t border-border px-6 pt-12 pb-24 sm:px-8 sm:pt-16 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1100px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('education.timeline') || 'Timeline' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(education.length) }}
                </span>
            </div>

            <ol class="border-t border-border">
                <li
                    v-for="(edu, i) in education"
                    :key="edu.id"
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
                                    v-if="edu.is_current"
                                    class="inline-flex items-center gap-1.5 text-foreground"
                                >
                                    <span
                                        class="status-dot"
                                        aria-hidden="true"
                                    />
                                    {{ t('education.current') || 'Current' }}
                                </span>
                                <span
                                    v-else-if="edu.is_featured"
                                    class="text-foreground"
                                >
                                    {{ t('education.featured') || 'Featured' }}
                                </span>
                            </div>
                            <div
                                class="font-mono text-[11px] tracking-[0.12em] text-foreground uppercase"
                            >
                                {{ formatDate(edu.start_date) }}
                                <span class="text-muted-foreground">—</span>
                                {{
                                    edu.is_current
                                        ? t('education.present') || 'Present'
                                        : edu.end_date
                                          ? formatDate(edu.end_date)
                                          : ''
                                }}
                            </div>
                            <div
                                v-if="edu.location"
                                class="inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                <MapPin
                                    class="h-3 w-3 shrink-0"
                                    aria-hidden="true"
                                />
                                {{ edu.location }}
                            </div>
                        </div>

                        <!-- Right: degree + institution + body -->
                        <div class="min-w-0">
                            <h2
                                class="text-xl font-semibold tracking-[-0.02em] text-foreground sm:text-2xl"
                            >
                                {{
                                    edu.degree ||
                                    t('education.studies') ||
                                    'Studies'
                                }}<span class="text-accent">.</span>
                            </h2>

                            <p
                                v-if="edu.field_of_study"
                                class="mt-2 max-w-[60ch] text-base leading-relaxed text-pretty text-muted-foreground"
                            >
                                {{ edu.field_of_study }}
                            </p>

                            <div
                                class="mt-3 inline-flex items-center gap-2 font-mono text-[11px] tracking-[0.12em] text-foreground uppercase"
                            >
                                <GraduationCap
                                    class="h-3 w-3 shrink-0"
                                    aria-hidden="true"
                                />
                                {{ edu.institution }}
                            </div>

                            <p
                                v-if="edu.description"
                                class="mt-5 max-w-[70ch] text-base leading-relaxed text-pretty text-muted-foreground"
                            >
                                {{ edu.description }}
                            </p>

                            <ul
                                v-if="edu.achievements?.length"
                                class="mt-5 space-y-2 border-l border-border pl-5"
                            >
                                <li
                                    v-for="(a, idx) in edu.achievements"
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
                {{ t('education.empty') || 'Education details coming soon' }}
            </p>
        </div>
    </section>
</template>
