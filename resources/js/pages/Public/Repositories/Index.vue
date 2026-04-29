<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ExternalLink, GitFork, Github, Star } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface RepositoryItem {
    id: number;
    name: string;
    slug: string;
    url: string;
    summary: string | null;
    owner: string | null;
    language: string | null;
    technologies: string[] | null;
    stars: number;
    forks: number;
    status: string;
    is_featured: boolean;
    demo_url: string | null;
    thumbnail: string | null;
}

interface Props {
    repositories: RepositoryItem[];
    featured: RepositoryItem[];
}

defineProps<Props>();

function getStatusColor(status: string): string {
    switch (status) {
        case 'active':
            return 'bg-green-500/10 text-green-600 dark:text-green-400';
        case 'experimental':
            return 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400';
        case 'archived':
            return 'bg-gray-500/10 text-gray-600 dark:text-gray-400';
        case 'deprecated':
            return 'bg-red-500/10 text-red-600 dark:text-red-400';
        default:
            return 'bg-gray-500/10 text-gray-600';
    }
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold tracking-tight">Open Source</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Projects and contributions I've made to the open source
                    community
                </p>
            </div>

            <!-- Featured Repositories -->
            <div v-if="featured.length" class="mb-16">
                <h2 class="mb-6 text-2xl font-semibold">Featured</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <Link
                        v-for="repo in featured"
                        :key="repo.id"
                        :href="`/repositories/${repo.slug}`"
                        class="group block"
                    >
                        <Card
                            class="h-full transition-all hover:border-primary/50 hover:shadow-lg"
                        >
                            <CardHeader>
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="min-w-0 flex-1">
                                        <CardTitle
                                            class="flex items-center gap-2"
                                        >
                                            <span
                                                class="truncate group-hover:text-primary"
                                                >{{ repo.name }}</span
                                            >
                                        </CardTitle>
                                        <p
                                            v-if="repo.owner"
                                            class="mt-1 text-sm text-muted-foreground"
                                        >
                                            {{ repo.owner }}
                                        </p>
                                    </div>
                                    <Badge
                                        :class="getStatusColor(repo.status)"
                                        variant="secondary"
                                    >
                                        {{ repo.status }}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <p
                                    v-if="repo.summary"
                                    class="mb-4 text-muted-foreground"
                                >
                                    {{ repo.summary }}
                                </p>

                                <div class="flex flex-wrap items-center gap-4">
                                    <Badge
                                        v-if="repo.language"
                                        variant="outline"
                                    >
                                        {{ repo.language }}
                                    </Badge>

                                    <div
                                        class="flex items-center gap-1 text-sm text-muted-foreground"
                                    >
                                        <Star class="h-4 w-4" />
                                        <span>{{ repo.stars }}</span>
                                    </div>

                                    <div
                                        class="flex items-center gap-1 text-sm text-muted-foreground"
                                    >
                                        <GitFork class="h-4 w-4" />
                                        <span>{{ repo.forks }}</span>
                                    </div>
                                </div>

                                <div
                                    v-if="repo.technologies?.length"
                                    class="mt-4 flex flex-wrap gap-2"
                                >
                                    <Badge
                                        v-for="tech in repo.technologies.slice(
                                            0,
                                            5,
                                        )"
                                        :key="tech"
                                        variant="secondary"
                                        class="text-xs"
                                    >
                                        {{ tech }}
                                    </Badge>
                                </div>

                                <div class="mt-6 flex gap-3">
                                    <Button
                                        as-child
                                        size="sm"
                                        variant="outline"
                                        @click.stop
                                    >
                                        <a
                                            :href="repo.url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            :aria-label="`View ${repo.name} on GitHub`"
                                        >
                                            <Github class="mr-1 h-4 w-4" />
                                            GitHub
                                            <ExternalLink
                                                class="ml-1 h-3 w-3"
                                            />
                                        </a>
                                    </Button>
                                    <Button
                                        v-if="repo.demo_url"
                                        as-child
                                        variant="outline"
                                        size="sm"
                                        @click.stop
                                    >
                                        <a
                                            :href="repo.demo_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            :aria-label="`Open ${repo.name} live demo`"
                                        >
                                            Live Demo
                                        </a>
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>

            <!-- All Repositories -->
            <div v-if="repositories.length">
                <h2 class="mb-6 text-2xl font-semibold">All Repositories</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="repo in repositories"
                        :key="repo.id"
                        class="group relative rounded-lg border border-border/40 bg-card p-5 transition-all hover:border-primary/50 hover:shadow-md"
                    >
                        <Link
                            :href="`/repositories/${repo.slug}`"
                            class="absolute inset-0 z-0 rounded-lg"
                            :aria-label="`View ${repo.name} details`"
                        />
                        <div class="pointer-events-none relative z-10">
                            <div class="flex items-start justify-between gap-2">
                                <h3
                                    class="font-medium group-hover:text-primary"
                                >
                                    {{ repo.name }}
                                </h3>
                                <a
                                    :href="repo.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="`View ${repo.name} on GitHub`"
                                    class="pointer-events-auto -mt-1 -mr-1 rounded p-1 text-muted-foreground transition-colors hover:text-primary"
                                    @click.stop
                                >
                                    <Github class="h-4 w-4" />
                                </a>
                            </div>

                            <p
                                v-if="repo.summary"
                                class="mt-2 line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ repo.summary }}
                            </p>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <Badge
                                    v-if="repo.language"
                                    variant="outline"
                                    class="text-xs"
                                >
                                    {{ repo.language }}
                                </Badge>

                                <div
                                    class="flex items-center gap-1 text-xs text-muted-foreground"
                                >
                                    <Star class="h-3 w-3" />
                                    <span>{{ repo.stars }}</span>
                                </div>

                                <div
                                    class="flex items-center gap-1 text-xs text-muted-foreground"
                                >
                                    <GitFork class="h-3 w-3" />
                                    <span>{{ repo.forks }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!repositories.length" class="py-20 text-center">
                <p class="text-lg font-medium">Repositories coming soon</p>
                <p class="mt-2 text-muted-foreground">
                    Check back later for open source projects
                </p>
            </div>
        </div>
    </section>
</template>
