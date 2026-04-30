<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { useT } from '@/composables/useTranslate';

interface SkillItem {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    proficiency_label: string | null;
    proficiency_score: number;
    years_experience: number | null;
    icon: string | null;
}

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
    logo: string | null;
}

interface HobbyItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    icon: string | null;
    is_featured: boolean;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    skills: SkillItem[];
    experiences: ExperienceItem[];
    hobbies: HobbyItem[];
}

const props = defineProps<Props>();

const { t, locale } = useT();

const groupedSkills = computed(() => {
    const groups: Record<string, SkillItem[]> = {};

    for (const skill of props.skills) {
        const cat = skill.category || 'Other';

        if (!groups[cat]) {
            groups[cat] = [];
        }

        groups[cat].push(skill);
    }

    return groups;
});

function isUrl(icon: string | null): boolean {
    return !!icon && (icon.startsWith('http') || icon.startsWith('/'));
}

function getDeviconClass(icon: string): string | null {
    const match = icon.match(/class="([^"]+)"/);

    if (match) {
        return match[1];
    }

    if (icon.startsWith('devicon-')) {
        return icon;
    }

    return null;
}

function formatMonthYear(date: string): string {
    return new Date(date).toLocaleDateString(locale.value || 'en', {
        year: 'numeric',
        month: 'short',
    });
}

const portrait = computed<string | null>(() => {
    const path = props.settings?.about_image;

    if (!path) {
        return null;
    }

    return path.startsWith('http') || path.startsWith('/')
        ? path
        : `/storage/${path}`;
});

const intro = computed<string | null>(
    () => props.settings?.about_intro || null,
);
</script>

<template>
    <!-- ============ HERO ============ -->
    <section class="px-6 pt-24 pb-24 sm:px-8 sm:pt-32 sm:pb-32 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.about.eyebrow') }}</span>

            <div
                class="mt-10 grid gap-12 lg:grid-cols-[1.1fr_minmax(0,1fr)] lg:items-start lg:gap-16"
            >
                <div>
                    <h1
                        class="max-w-[18ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
                    >
                        Engineer behind quietly reliable web products<span
                            class="text-accent"
                            >.</span
                        >
                    </h1>

                    <div
                        v-if="intro"
                        class="mt-10 max-w-[55ch] text-lg leading-relaxed text-pretty text-foreground"
                    >
                        {{ intro }}
                    </div>

                    <div
                        class="mt-10 max-w-[55ch] space-y-5 text-lg leading-relaxed text-pretty text-muted-foreground"
                    >
                        <p>
                            Hi, I'm
                            <span class="font-medium text-foreground"
                                >Giorgi</span
                            >
                            — a full-stack engineer who builds web products end
                            to end.
                        </p>

                        <p>
                            I work mostly with
                            <span class="font-medium text-foreground"
                                >Laravel</span
                            >
                            on the backend and
                            <span class="font-medium text-foreground">Vue</span>
                            on the frontend, and I care about the small details
                            that make a product feel finished.
                        </p>

                        <p>
                            Over the years I've shipped admin panels, payment
                            integrations, financial dashboards, and the kind of
                            unglamorous internal tooling that quietly keeps real
                            businesses moving.
                        </p>
                    </div>

                    <div class="mt-12 flex flex-wrap gap-4">
                        <Link
                            href="/contact"
                            class="group inline-flex items-center gap-2 bg-foreground px-5 py-3 text-sm font-medium text-background transition-opacity hover:opacity-90 active:scale-[0.98]"
                        >
                            {{ t('cta.get_in_touch') }}
                            <ArrowUpRight
                                class="h-4 w-4 text-accent transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            />
                        </Link>
                        <Link
                            href="/projects"
                            class="inline-flex items-center gap-2 border border-border bg-transparent px-5 py-3 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                        >
                            {{ t('cta.view_projects') }}
                        </Link>
                    </div>
                </div>

                <figure
                    v-if="portrait"
                    class="relative mx-auto w-full max-w-sm self-start lg:mx-0 lg:max-w-none"
                >
                    <div
                        class="aspect-[4/5] w-full overflow-hidden border border-border bg-muted"
                    >
                        <img
                            :src="portrait"
                            :alt="t('about.portrait_alt')"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <figcaption
                        class="mt-3 font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                    >
                        {{ t('about.portrait_alt') }}
                        <span v-if="settings?.location">
                            · {{ settings.location }}</span
                        >
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- ============ SKILLS ============ -->
    <section
        v-if="props.skills.length"
        class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.skills.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('about.skills_title') }}
                    </h2>
                    <p
                        class="mt-4 max-w-[55ch] text-base leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ t('about.skills_lead') }}
                    </p>
                </div>
                <Link
                    href="/skills"
                    class="group inline-flex shrink-0 items-center gap-1 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    {{ t('actions.view_all') }}
                    <ArrowUpRight
                        class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </Link>
            </div>

            <div
                v-for="(skillsInGroup, category) in groupedSkills"
                :key="category"
                class="mb-10 last:mb-0"
            >
                <div
                    class="mb-4 font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ category }}
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <span
                        v-for="skill in skillsInGroup"
                        :key="skill.id"
                        class="inline-flex items-center gap-2 border border-border bg-background px-3 py-1.5 font-mono text-xs text-foreground"
                    >
                        <img
                            v-if="isUrl(skill.icon)"
                            :src="skill.icon ?? ''"
                            :alt="skill.name"
                            class="h-3.5 w-3.5 object-contain"
                        />
                        <i
                            v-else-if="
                                skill.icon && getDeviconClass(skill.icon)
                            "
                            :class="getDeviconClass(skill.icon)"
                            class="text-sm"
                        />
                        {{ skill.name }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ EXPERIENCE ============ -->
    <section
        v-if="experiences.length"
        class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.experience.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('about.experience_title') }}
                    </h2>
                </div>
                <Link
                    href="/experience"
                    class="group inline-flex shrink-0 items-center gap-1 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    {{ t('actions.full_timeline') }}
                    <ArrowUpRight
                        class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </Link>
            </div>

            <ul class="border-t border-border">
                <li
                    v-for="exp in experiences"
                    :key="exp.id"
                    class="grid grid-cols-1 gap-6 border-b border-border py-8 sm:grid-cols-[180px_1fr]"
                >
                    <div
                        class="font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                    >
                        <div>
                            {{ formatMonthYear(exp.start_date) }} —
                            <template v-if="exp.is_current">{{
                                t('experience.current')
                            }}</template>
                            <template v-else-if="exp.end_date">{{
                                formatMonthYear(exp.end_date)
                            }}</template>
                        </div>
                        <div v-if="exp.is_current" class="mt-1 text-accent">
                            {{ t('experience.current') }}
                        </div>
                    </div>
                    <div>
                        <h3
                            class="text-lg font-semibold tracking-[-0.01em] text-foreground"
                        >
                            {{ exp.role }}
                        </h3>
                        <div
                            class="mt-1 font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                        >
                            {{ exp.company }}
                        </div>
                        <p
                            v-if="exp.summary"
                            class="mt-4 max-w-[65ch] text-sm leading-relaxed text-pretty text-muted-foreground"
                        >
                            {{ exp.summary }}
                        </p>
                        <div
                            v-if="exp.technologies?.length"
                            class="mt-4 flex flex-wrap gap-1.5"
                        >
                            <span
                                v-for="tech in exp.technologies"
                                :key="tech"
                                class="border border-border px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                            >
                                {{ tech }}
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <!-- ============ HOBBIES ============ -->
    <section
        v-if="hobbies.length"
        class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16">
                <span class="eyebrow">{{ t('sections.hobbies.eyebrow') }}</span>
                <h2
                    class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                >
                    {{ t('about.hobbies_title') }}
                </h2>
                <p
                    class="mt-4 max-w-[55ch] text-base leading-relaxed text-pretty text-muted-foreground"
                >
                    {{ t('about.hobbies_lead') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-px bg-border md:grid-cols-3">
                <div
                    v-for="hobby in hobbies.slice(0, 6)"
                    :key="hobby.id"
                    class="bg-background p-8"
                >
                    <h3 class="text-lg font-semibold tracking-[-0.01em]">
                        {{ hobby.title }}
                    </h3>
                    <p
                        v-if="hobby.summary"
                        class="mt-3 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ hobby.summary }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
