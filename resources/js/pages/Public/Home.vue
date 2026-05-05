<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Code2, Github, Mail } from 'lucide-vue-next';
import { computed } from 'vue';
import { useLocalePath } from '@/composables/useLocalePath';
import { useT } from '@/composables/useTranslate';

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    cover: string | null;
    tech_stack: string[] | null;
    year: number | null;
}

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover: string | null;
    publish_at: string;
    reading_time: number;
}

interface ServiceItem {
    id: number;
    title: string;
    slug: string;
    summary: string;
    icon: string | null;
}

interface SkillItem {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    proficiency_label: string | null;
    proficiency_score: number;
    icon: string | null;
}

interface TestimonialItem {
    id: number;
    author_name: string;
    author_role: string | null;
    company: string | null;
    quote: string;
    avatar: string | null;
    rating: number | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    featuredProjects: Project[];
    latestArticles: ArticleItem[];
    services: ServiceItem[];
    skills: SkillItem[];
    testimonials: TestimonialItem[];
}

const props = defineProps<Props>();

const { t, locale } = useT();
const localePath = useLocalePath();

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

const capabilities = [
    { num: '01', key: 'greenfield' },
    { num: '02', key: 'rewrites' },
    { num: '03', key: 'senior' },
] as const;

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function formatPublishedAt(date: string): string {
    return new Date(date).toLocaleDateString(locale.value || 'en', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    });
}

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
</script>

<template>
    <!-- ============ HERO ============ -->
    <section class="px-6 pt-24 pb-32 sm:px-8 sm:pt-32 sm:pb-40 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <div class="reveal reveal-1 mb-12 flex items-center gap-3">
                <span class="status-dot" aria-hidden="true" />
                <span
                    class="font-mono text-xs font-medium tracking-[0.16em] text-muted-foreground uppercase"
                >
                    {{ t('hero.status') }} · {{ t('hero.location') }}
                </span>
            </div>

            <h1
                class="reveal reveal-2 max-w-[18ch] text-[clamp(2.75rem,7vw,5rem)] leading-[1.04] font-semibold tracking-[-0.035em] text-balance"
            >
                {{ t('hero.title_1') }}<br />{{ t('hero.title_2')
                }}<span class="text-accent">.</span>
            </h1>

            <p
                class="reveal reveal-3 mt-10 max-w-[55ch] text-[clamp(1.125rem,1.4vw,1.25rem)] leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('hero.lead') }}
            </p>

            <div class="reveal reveal-4 mt-12 flex flex-wrap gap-4">
                <Link
                    href="/projects"
                    class="group inline-flex items-center gap-2 bg-foreground px-5 py-3 text-sm font-medium text-background transition-opacity hover:opacity-90 active:scale-[0.98]"
                >
                    {{ t('cta.view_work') }}
                    <ArrowUpRight
                        class="h-4 w-4 text-accent transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </Link>
                <Link
                    href="/contact"
                    class="inline-flex items-center gap-2 border border-border bg-transparent px-5 py-3 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                >
                    {{ t('cta.write_to_me') }}
                </Link>
            </div>

            <div
                class="reveal reveal-4 mt-20 grid grid-cols-2 gap-8 border-t border-border pt-8 sm:grid-cols-4"
            >
                <div>
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ t('hero.meta.years') }}
                    </div>
                    <div class="mt-2 font-mono text-2xl">2018 → 2025</div>
                </div>
                <div>
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ t('hero.meta.role') }}
                    </div>
                    <div class="mt-2 text-base">
                        {{ t('hero.meta.role_value') }}
                    </div>
                </div>
                <div>
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ t('hero.meta.stack') }}
                    </div>
                    <div class="mt-2 font-mono text-sm">
                        PHP · TS · Vue · Laravel
                    </div>
                </div>
                <div>
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ t('hero.meta.based') }}
                    </div>
                    <div class="mt-2 text-base">
                        {{ settings?.location || t('hero.location') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SELECTED WORK ============ -->
    <section
        v-if="featuredProjects.length"
        class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.work.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('sections.work.title') }}
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

            <div
                class="grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="(project, i) in featuredProjects"
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
                            v-if="project.cover"
                            :src="project.cover"
                            :alt="project.title"
                            class="h-full w-full object-cover"
                        />
                        <Code2
                            v-else
                            class="h-8 w-8 text-muted-foreground"
                            :stroke-width="1.5"
                        />
                    </div>
                    <h3
                        class="mt-6 flex items-center gap-1 text-lg font-semibold tracking-[-0.01em]"
                    >
                        {{ project.title }}
                        <ArrowUpRight
                            class="h-3.5 w-3.5 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                        />
                    </h3>
                    <p
                        class="mt-3 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ project.summary }}
                    </p>
                    <div
                        v-if="project.tech_stack?.length"
                        class="mt-6 flex flex-wrap gap-1.5 pt-6"
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

    <!-- ============ CAPABILITIES ============ -->
    <section
        class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16">
                <span class="eyebrow">{{
                    t('sections.capabilities.eyebrow')
                }}</span>
                <h2
                    class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                >
                    {{ t('sections.capabilities.title') }}
                </h2>
                <p
                    class="mt-4 max-w-[55ch] text-lg leading-relaxed text-pretty text-muted-foreground"
                >
                    {{ t('sections.capabilities.lead') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-px bg-border md:grid-cols-3">
                <div
                    v-for="cap in capabilities"
                    :key="cap.key"
                    class="bg-background p-8"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / {{ cap.num }}
                    </div>
                    <h3 class="mt-6 text-xl font-semibold tracking-[-0.01em]">
                        {{ t(`capabilities.${cap.key}.title`) }}
                    </h3>
                    <p
                        class="mt-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ t(`capabilities.${cap.key}.body`) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SERVICES ============ -->
    <section
        v-if="services.length"
        class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.services.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('home.services.title') }}
                    </h2>
                </div>
                <Link
                    href="/services"
                    class="group inline-flex shrink-0 items-center gap-1 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    {{ t('actions.view_all') }}
                    <ArrowUpRight
                        class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-px bg-border md:grid-cols-3">
                <div
                    v-for="(service, i) in services"
                    :key="service.id"
                    class="flex flex-col bg-background p-8"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / {{ pad(i + 1) }}
                    </div>
                    <h3 class="mt-6 text-xl font-semibold tracking-[-0.01em]">
                        {{ service.title }}
                    </h3>
                    <p
                        class="mt-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ service.summary }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ STACK ============ -->
    <section
        v-if="props.skills.length"
        class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.stack.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('sections.stack.title') }}
                    </h2>
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
                        class="inline-flex cursor-default items-center gap-2 border border-border bg-background px-3 py-1.5 font-mono text-xs text-foreground transition-colors hover:border-foreground hover:bg-muted/40"
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

    <!-- ============ FEEDBACK ============ -->
    <section
        v-if="testimonials.length"
        class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16">
                <span class="eyebrow">{{
                    t('sections.feedback.eyebrow')
                }}</span>
                <h2
                    class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                >
                    {{ t('home.feedback.title') }}
                </h2>
            </div>

            <div
                class="grid grid-cols-1 gap-px bg-border md:grid-cols-2 lg:grid-cols-3"
            >
                <figure
                    v-for="testimonial in testimonials"
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
                                testimonial.author_role || testimonial.company
                            "
                            class="mt-1"
                        >
                            {{
                                [testimonial.author_role, testimonial.company]
                                    .filter(Boolean)
                                    .join(' · ')
                            }}
                        </div>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- ============ JOURNAL ============ -->
    <section
        v-if="latestArticles.length"
        class="border-t border-border px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 flex items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">{{
                        t('sections.journal.eyebrow')
                    }}</span>
                    <h2
                        class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em]"
                    >
                        {{ t('sections.journal.title') }}
                    </h2>
                </div>
                <Link
                    href="/blog"
                    class="group inline-flex shrink-0 items-center gap-1 font-mono text-xs font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    {{ t('actions.all_writing') }}
                    <ArrowUpRight
                        class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </Link>
            </div>

            <ul class="border-t border-border">
                <li
                    v-for="(article, i) in latestArticles"
                    :key="article.id"
                    class="border-b border-border"
                >
                    <Link
                        :href="localePath(`/blog/${article.slug}`)"
                        class="group grid grid-cols-[40px_1fr_auto] items-baseline gap-6 py-6 transition-colors hover:bg-muted/30 sm:grid-cols-[40px_1fr_auto_auto]"
                    >
                        <span
                            class="font-mono text-[11px] text-muted-foreground"
                            >{{ pad(i + 1) }}</span
                        >
                        <h3
                            class="text-[clamp(1.125rem,1.4vw,1.25rem)] leading-tight font-medium tracking-[-0.01em]"
                        >
                            {{ article.title }}
                            <ArrowUpRight
                                class="ml-1 inline-block h-3.5 w-3.5 -translate-y-px text-muted-foreground transition-colors group-hover:text-accent"
                            />
                        </h3>
                        <span
                            class="hidden font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase sm:inline"
                            >{{ article.reading_time }}
                            {{ t('article.min') }}</span
                        >
                        <time
                            class="font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                            >{{ formatPublishedAt(article.publish_at) }}</time
                        >
                    </Link>
                </li>
            </ul>
        </div>
    </section>

    <!-- ============ CONTACT ============ -->
    <section
        class="border-t border-border bg-foreground px-6 py-24 text-background sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow" style="color: hsl(var(--background) / 0.6)">{{
                t('sections.contact.eyebrow')
            }}</span>
            <h2
                class="mt-6 max-w-[18ch] text-[clamp(2.25rem,5vw,3.75rem)] leading-[1.05] font-semibold tracking-[-0.025em] text-balance"
            >
                {{ t('sections.contact.title')
                }}<span class="text-accent">.</span>
            </h2>
            <p
                class="mt-8 max-w-[55ch] text-lg leading-relaxed text-pretty"
                style="color: hsl(var(--background) / 0.7)"
            >
                {{ t('sections.contact.lead') }}
            </p>
            <div class="mt-12 flex flex-wrap items-center gap-6">
                <a
                    v-if="settings?.email"
                    :href="`mailto:${settings.email}`"
                    class="inline-flex items-center gap-3 border px-5 py-3 text-sm font-medium transition-colors"
                    style="
                        border-color: hsl(var(--background) / 0.2);
                        color: hsl(var(--background));
                    "
                >
                    <Mail class="h-4 w-4" :stroke-width="1.5" />
                    {{ settings.email }}
                </a>
                <a
                    href="https://github.com/GiorgiGrdzelidze"
                    target="_blank"
                    rel="noopener"
                    class="group inline-flex items-center gap-3 text-sm font-medium transition-colors"
                    style="color: hsl(var(--background) / 0.8)"
                >
                    <Github class="h-4 w-4" :stroke-width="1.5" />
                    {{ t('sections.contact.github') }}
                    <ArrowUpRight class="h-3.5 w-3.5 text-accent" />
                </a>
            </div>
        </div>
    </section>
</template>
