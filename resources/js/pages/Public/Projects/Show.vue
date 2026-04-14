<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, ExternalLink, Github } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

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
    metrics: Array<{ label: string; value: string }> | null;
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
    project: Project;
    relatedProjects: RelatedProject[];
}

defineProps<Props>();
</script>

<template>
    <article class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <Link href="/projects" class="mb-8 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Back to Projects
            </Link>

            <!-- Header -->
            <header>
                <h1 class="text-4xl font-bold tracking-tight">{{ project.title }}</h1>
                <p v-if="project.summary" class="mt-4 text-lg text-muted-foreground">{{ project.summary }}</p>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                    <span v-if="project.role" class="flex items-center gap-1">
                        Role: <strong class="text-foreground">{{ project.role }}</strong>
                    </span>
                    <span v-if="project.year" class="flex items-center gap-1">
                        <Calendar class="h-4 w-4" />
                        {{ project.year }}
                    </span>
                    <span v-if="project.industry">{{ project.industry }}</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Button v-if="project.live_url" as-child size="sm">
                        <a :href="project.live_url" target="_blank" rel="noopener noreferrer">
                            <ExternalLink class="mr-1.5 h-4 w-4" />
                            Live Site
                        </a>
                    </Button>
                    <Button v-if="project.repo_url" as-child variant="outline" size="sm">
                        <a :href="project.repo_url" target="_blank" rel="noopener noreferrer">
                            <Github class="mr-1.5 h-4 w-4" />
                            Source Code
                        </a>
                    </Button>
                </div>
            </header>

            <!-- Cover Image -->
            <div v-if="project.cover_image" class="mt-8 overflow-hidden rounded-xl">
                <img :src="`/storage/${project.cover_image}`" :alt="project.title" class="w-full" />
            </div>

            <Separator class="my-10" />

            <!-- Description -->
            <div v-if="project.description" class="prose prose-neutral dark:prose-invert max-w-none" v-html="project.description" />

            <!-- Case Study Sections -->
            <div v-if="project.challenge" class="mt-10">
                <h2 class="text-2xl font-bold">The Challenge</h2>
                <p class="mt-3 text-muted-foreground leading-relaxed">{{ project.challenge }}</p>
            </div>
            <div v-if="project.solution" class="mt-8">
                <h2 class="text-2xl font-bold">The Solution</h2>
                <p class="mt-3 text-muted-foreground leading-relaxed">{{ project.solution }}</p>
            </div>
            <div v-if="project.process" class="mt-8">
                <h2 class="text-2xl font-bold">The Process</h2>
                <p class="mt-3 text-muted-foreground leading-relaxed">{{ project.process }}</p>
            </div>

              <!-- Metrics -->
            <div v-if="project.metrics?.length" class="mt-12">
                <h2 class="text-2xl font-bold">Project Highlights</h2>
                <div class="mt-4 divide-y divide-border/50 rounded-xl border border-border/50">
                    <div
                        v-for="metric in project.metrics"
                        :key="metric.label"
                        class="flex items-start gap-4 p-4 sm:items-center sm:gap-6"
                    >
                        <span class="shrink-0 text-xs font-medium uppercase tracking-wider text-muted-foreground sm:w-40">{{ metric.label }}</span>
                        <span class="text-sm text-foreground">{{ metric.value }}</span>
                    </div>
                </div>
            </div>

            <!-- Tech Stack -->
            <div v-if="project.tech_stack?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Tech Stack</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <Badge v-for="tech in project.tech_stack" :key="tech" variant="secondary">{{ tech }}</Badge>
                </div>
            </div>

            <!-- Skills Used -->
            <div v-if="project.skills?.length" class="mt-8">
                <h2 class="text-2xl font-bold">Skills</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <Badge v-for="skill in project.skills" :key="skill.id" variant="outline">{{ skill.name }}</Badge>
                </div>
            </div>

            <!-- Testimonials -->
            <div v-if="project.testimonials?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Client Feedback</h2>
                <div class="mt-4 space-y-4">
                    <Card v-for="t in project.testimonials" :key="t.id">
                        <CardContent class="pt-6">
                            <blockquote class="text-sm italic text-muted-foreground">"{{ t.quote }}"</blockquote>
                            <p class="mt-3 text-sm font-medium">
                                — {{ t.author_name }}
                                <span v-if="t.company" class="text-muted-foreground">, {{ t.company }}</span>
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Gallery -->
            <div v-if="project.gallery?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Gallery</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div v-for="(img, idx) in project.gallery" :key="idx" class="overflow-hidden rounded-lg">
                        <img :src="`/storage/${img}`" :alt="`${project.title} screenshot ${idx + 1}`" class="w-full" />
                    </div>
                </div>
            </div>


        </div>

        <!-- Related Projects -->
        <div v-if="relatedProjects.length" class="mt-16 border-t border-border/40 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold">More Projects</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    <Link
                        v-for="rp in relatedProjects"
                        :key="rp.id"
                        :href="`/projects/${rp.slug}`"
                        class="group"
                    >
                        <Card class="h-full transition-all hover:shadow-md hover:border-foreground/20">
                            <CardHeader>
                                <CardTitle class="text-base group-hover:text-primary/80">{{ rp.title }}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="line-clamp-2 text-sm text-muted-foreground">{{ rp.summary }}</p>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </article>
</template>
