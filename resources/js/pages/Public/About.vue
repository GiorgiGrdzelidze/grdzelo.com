<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    Calendar,
    MapPin,
    Sparkles,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

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
    skills: SkillItem[];
    experiences: ExperienceItem[];
    hobbies: HobbyItem[];
}

const props = defineProps<Props>();

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
    // Extract class from HTML tag like <i class="devicon-php-plain"></i>
    const match = icon.match(/class="([^"]+)"/);

    if (match) {
        return match[1];
    }

    // Already a class string like "devicon-php-plain"
    if (icon.startsWith('devicon-')) {
        return icon;
    }

    return null;
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
    });
}
</script>

<template>
    <div>
        <!-- Hero -->
        <section class="relative overflow-hidden py-20 sm:py-28">
            <!-- Decorative gradient backdrop -->
            <div
                class="pointer-events-none absolute inset-0 -z-10"
                aria-hidden="true"
            >
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"
                />
                <div
                    class="absolute -top-32 -left-20 h-72 w-[640px] rounded-full bg-primary/15 blur-3xl"
                />
            </div>

            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_1fr]">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-border/60 bg-card/60 px-3 py-1 text-xs font-medium tracking-wider text-muted-foreground uppercase shadow-sm backdrop-blur"
                        >
                            <span class="relative flex h-1.5 w-1.5">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"
                                />
                                <span
                                    class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"
                                />
                            </span>
                            Available for new work
                        </span>

                        <h1
                            class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
                        >
                            Engineer behind
                            <span class="relative whitespace-nowrap">
                                <span
                                    class="relative bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent"
                                >
                                    quietly reliable
                                </span>
                            </span>
                            web products.
                        </h1>

                        <div
                            class="mt-6 max-w-prose space-y-5 text-lg leading-8 text-foreground/80"
                        >
                            <p>
                                Hi, I'm
                                <span class="font-semibold text-foreground"
                                    >Giorgi</span
                                >
                                — a full-stack engineer who builds web products
                                end to end.
                            </p>

                            <p>
                                I work mostly with
                                <span class="font-semibold text-foreground"
                                    >Laravel</span
                                >
                                on the backend and
                                <span class="font-semibold text-foreground"
                                    >Vue</span
                                >
                                on the frontend, and I care about the small
                                details that make a product feel finished.
                            </p>

                            <p>
                                Over the years I've shipped admin panels,
                                payment integrations, financial dashboards, and
                                the kind of unglamorous internal tooling that
                                quietly keeps real businesses moving.
                            </p>
                        </div>

                        <!-- Quick facts -->
                        <ul
                            class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted-foreground"
                        >
                            <li class="inline-flex items-center gap-1.5">
                                <MapPin class="h-4 w-4 text-primary/70" />
                                Tbilisi, Georgia
                            </li>
                            <li class="inline-flex items-center gap-1.5">
                                <Briefcase class="h-4 w-4 text-primary/70" />
                                Full-stack · Laravel · Vue
                            </li>
                            <li class="inline-flex items-center gap-1.5">
                                <Sparkles class="h-4 w-4 text-primary/70" />
                                Open to freelance & contract
                            </li>
                        </ul>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            <Button as-child size="lg">
                                <Link href="/contact">
                                    Get in Touch
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                            <Button as-child variant="outline" size="lg">
                                <Link href="/projects">View Projects</Link>
                            </Button>
                        </div>
                    </div>

                    <div class="flex justify-center lg:justify-end">
                        <div class="relative">
                            <div
                                class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-primary/20 via-primary/5 to-transparent blur-2xl"
                                aria-hidden="true"
                            />
                            <div
                                class="relative aspect-square w-72 overflow-hidden rounded-2xl border border-border/60 bg-card/40 shadow-xl shadow-primary/5 backdrop-blur sm:w-80"
                            >
                                <div
                                    class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-transparent"
                                />
                                <div
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <span
                                        class="bg-gradient-to-br from-foreground/30 to-foreground/10 bg-clip-text font-mono text-7xl font-bold text-transparent select-none"
                                    >
                                        GG
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Skills Preview -->
        <section
            v-if="props.skills.length"
            class="border-t border-border/40 bg-muted/30 py-20"
        >
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">
                            Skills & Expertise
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Technologies and tools I work with
                        </p>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/skills">
                            View All
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </div>

                <div
                    v-for="(skills, category) in groupedSkills"
                    :key="category"
                    class="mt-8"
                >
                    <h3 class="mb-4 text-lg font-semibold">{{ category }}</h3>
                    <div
                        class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                    >
                        <div
                            v-for="skill in skills"
                            :key="skill.id"
                            class="flex items-center gap-3 rounded-lg border border-border/40 bg-background p-4"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-xl"
                            >
                                <img
                                    v-if="isUrl(skill.icon)"
                                    :src="skill.icon ?? ''"
                                    :alt="skill.name"
                                    class="h-6 w-6 object-contain"
                                />
                                <i
                                    v-else-if="
                                        skill.icon &&
                                        getDeviconClass(skill.icon)
                                    "
                                    :class="getDeviconClass(skill.icon)"
                                />
                                <span v-else class="text-lg">🔧</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">
                                    {{ skill.name }}
                                </p>
                                <!-- <span class="text-xs text-muted-foreground">{{ skill.proficiency_score }}%</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Experience Timeline -->
        <section
            v-if="experiences.length"
            class="border-t border-border/40 py-20"
        >
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">
                            Experience
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            My professional journey
                        </p>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/experience">
                            Full Timeline
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </div>

                <div class="mt-10 space-y-8">
                    <div
                        v-for="exp in experiences"
                        :key="exp.id"
                        class="relative pl-8 before:absolute before:top-2 before:left-0 before:h-3 before:w-3 before:rounded-full before:bg-primary before:content-[''] after:absolute after:top-5 after:left-[5px] after:h-full after:w-0.5 after:bg-border/40 after:content-[''] last:after:hidden"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                v-if="exp.logo"
                                class="hidden shrink-0 sm:block"
                            >
                                <img
                                    :src="`/storage/${exp.logo}`"
                                    :alt="exp.company"
                                    class="h-10 w-10 rounded-lg object-contain"
                                />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ exp.role }}</h3>
                                <p
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <Briefcase class="h-3.5 w-3.5" />
                                    {{ exp.company }}
                                    <span v-if="exp.is_current">
                                        <Badge
                                            variant="secondary"
                                            class="text-xs"
                                            >Current</Badge
                                        >
                                    </span>
                                </p>
                                <p
                                    class="mt-1 flex items-center gap-1 text-xs text-muted-foreground"
                                >
                                    <Calendar class="h-3 w-3" />
                                    {{ formatDate(exp.start_date) }} —
                                    {{
                                        exp.is_current
                                            ? 'Present'
                                            : exp.end_date
                                              ? formatDate(exp.end_date)
                                              : ''
                                    }}
                                </p>
                                <p
                                    v-if="exp.summary"
                                    class="mt-3 text-sm leading-relaxed text-muted-foreground"
                                >
                                    {{ exp.summary }}
                                </p>
                                <div
                                    v-if="exp.technologies?.length"
                                    class="mt-3 flex flex-wrap gap-1"
                                >
                                    <Badge
                                        v-for="tech in exp.technologies"
                                        :key="tech"
                                        variant="outline"
                                        class="text-xs"
                                        >{{ tech }}</Badge
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hobbies & Interests -->
        <section
            v-if="hobbies.length"
            class="border-t border-border/40 bg-muted/30 py-20"
        >
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">
                            Hobbies & Interests
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            What I enjoy outside of work
                        </p>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/hobbies">
                            View All
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="hobby in hobbies.slice(0, 6)"
                        :key="hobby.id"
                        class="rounded-lg border border-border/40 bg-background p-5"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                v-if="hobby.icon"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-lg"
                            >
                                {{ hobby.icon }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-medium">
                                        {{ hobby.title }}
                                    </h3>
                                    <Badge
                                        v-if="hobby.is_featured"
                                        variant="secondary"
                                        class="text-xs"
                                        >Featured</Badge
                                    >
                                </div>
                                <p
                                    v-if="hobby.summary"
                                    class="mt-1 line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{ hobby.summary }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
