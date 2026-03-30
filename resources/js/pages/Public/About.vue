<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Briefcase, Calendar } from 'lucide-vue-next';
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

interface Props {
    skills: SkillItem[];
    experiences: ExperienceItem[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
}
</script>

<template>
    <div>
        <!-- Hero -->
        <section class="py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <h1 class="text-4xl font-bold tracking-tight">About Me</h1>
                        <p class="mt-6 text-lg leading-relaxed text-muted-foreground">
                            I'm a product-minded software engineer who thrives at the intersection of engineering excellence
                            and user-centered design. I build web applications that are performant, maintainable, and delightful to use.
                        </p>
                        <p class="mt-4 text-muted-foreground leading-relaxed">
                            With a focus on Laravel, Vue.js, and modern web standards, I help teams and businesses
                            turn ideas into scalable, well-crafted digital products.
                        </p>
                        <div class="mt-8 flex gap-4">
                            <Button as-child>
                                <Link href="/contact">
                                    Get in Touch
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                            <Button as-child variant="outline">
                                <Link href="/projects">View Projects</Link>
                            </Button>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="h-80 w-80 rounded-2xl bg-muted/50 border border-border/40" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Skills Preview -->
        <section v-if="skills.length" class="border-t border-border/40 bg-muted/30 py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Skills & Expertise</h2>
                        <p class="mt-2 text-muted-foreground">Technologies and tools I work with</p>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/skills">
                            View All
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </div>
                <div class="mt-10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="skill in skills.slice(0, 12)"
                        :key="skill.id"
                        class="flex items-center gap-3 rounded-lg border border-border/40 bg-background p-4"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-sm font-semibold text-primary">
                            {{ skill.proficiency_score }}%
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ skill.name }}</p>
                            <p v-if="skill.category" class="truncate text-xs text-muted-foreground">{{ skill.category }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Experience Timeline -->
        <section v-if="experiences.length" class="border-t border-border/40 py-20">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Experience</h2>
                        <p class="mt-2 text-muted-foreground">My professional journey</p>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/experience">
                            Full Timeline
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </div>

                <div class="mt-10 space-y-8">
                    <div v-for="exp in experiences" :key="exp.id" class="relative pl-8 before:absolute before:left-0 before:top-2 before:h-3 before:w-3 before:rounded-full before:bg-primary before:content-[''] after:absolute after:left-[5px] after:top-5 after:h-full after:w-0.5 after:bg-border/40 after:content-[''] last:after:hidden">
                        <div class="flex items-start gap-4">
                            <div v-if="exp.logo" class="hidden shrink-0 sm:block">
                                <img :src="`/storage/${exp.logo}`" :alt="exp.company" class="h-10 w-10 rounded-lg object-contain" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ exp.role }}</h3>
                                <p class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Briefcase class="h-3.5 w-3.5" />
                                    {{ exp.company }}
                                    <span v-if="exp.is_current">
                                        <Badge variant="secondary" class="text-xs">Current</Badge>
                                    </span>
                                </p>
                                <p class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                    <Calendar class="h-3 w-3" />
                                    {{ formatDate(exp.start_date) }} — {{ exp.is_current ? 'Present' : (exp.end_date ? formatDate(exp.end_date) : '') }}
                                </p>
                                <p v-if="exp.summary" class="mt-3 text-sm text-muted-foreground leading-relaxed">{{ exp.summary }}</p>
                                <div v-if="exp.technologies?.length" class="mt-3 flex flex-wrap gap-1">
                                    <Badge v-for="tech in exp.technologies" :key="tech" variant="outline" class="text-xs">{{ tech }}</Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
