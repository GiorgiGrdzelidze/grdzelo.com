<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Code2, ExternalLink } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

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
}

interface Props {
    projects: Project[];
}

defineProps<Props>();
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Projects</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    A curated selection of work spanning web applications,
                    platforms, and technical solutions.
                </p>
            </div>

            <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="project in projects"
                    :key="project.id"
                    :href="`/projects/${project.slug}`"
                    class="group"
                >
                    <Card
                        class="h-full transition-all duration-200 hover:border-foreground/20 hover:shadow-lg"
                    >
                        <div
                            v-if="project.cover_image"
                            class="aspect-video overflow-hidden rounded-t-lg"
                        >
                            <img
                                :src="`/storage/${project.cover_image}`"
                                :alt="project.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <div
                            v-else
                            class="flex aspect-video items-center justify-center rounded-t-lg bg-muted"
                        >
                            <Code2 class="h-10 w-10 text-muted-foreground" />
                        </div>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-lg group-hover:text-primary/80"
                                    >{{ project.title }}</CardTitle
                                >
                                <ExternalLink
                                    class="h-4 w-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100"
                                />
                            </div>
                            <div
                                class="flex items-center gap-2 text-xs text-muted-foreground"
                            >
                                <span v-if="project.year">{{
                                    project.year
                                }}</span>
                                <span
                                    v-if="project.year && project.industry"
                                    class="text-border"
                                    >|</span
                                >
                                <span v-if="project.industry">{{
                                    project.industry
                                }}</span>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p
                                class="line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ project.summary }}
                            </p>
                            <div
                                v-if="project.tech_stack?.length"
                                class="mt-3 flex flex-wrap gap-1"
                            >
                                <Badge
                                    v-for="tech in project.tech_stack.slice(
                                        0,
                                        4,
                                    )"
                                    :key="tech"
                                    variant="secondary"
                                    class="text-xs"
                                >
                                    {{ tech }}
                                </Badge>
                                <Badge
                                    v-if="project.tech_stack.length > 4"
                                    variant="secondary"
                                    class="text-xs"
                                >
                                    +{{ project.tech_stack.length - 4 }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <div v-if="!projects.length" class="mt-14 text-center">
                <Code2 class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">No projects yet</p>
                <p class="mt-1 text-muted-foreground">
                    Check back soon for updates.
                </p>
            </div>
        </div>
    </section>
</template>
