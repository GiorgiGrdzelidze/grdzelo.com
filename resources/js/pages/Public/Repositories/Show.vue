<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ExternalLink,
    GitFork,
    Github,
    Star,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

interface RelatedProject {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    cover_image: string | null;
}

interface Repository {
    id: number;
    name: string;
    slug: string;
    url: string;
    summary: string | null;
    description: string | null;
    owner: string | null;
    language: string | null;
    technologies: string[] | null;
    stars: number;
    forks: number;
    status: string;
    is_featured: boolean;
    demo_url: string | null;
    thumbnail: string | null;
    screenshots: string[];
    project: RelatedProject | null;
}

interface RelatedArticle {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    cover_image: string | null;
    publish_at: string | null;
}

interface Props {
    repository: Repository;
    relatedArticles: RelatedArticle[];
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

function thumbnailUrl(path: string | null): string | null {
    if (!path) {
        return null;
    }

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('/')
    ) {
        return path;
    }

    return `/storage/${path}`;
}
</script>

<template>
    <article class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <Link
                href="/repositories"
                class="mb-8 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Repositories
            </Link>

            <!-- Header -->
            <header>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-4xl font-bold tracking-tight">
                            {{ repository.name }}
                        </h1>
                        <p
                            v-if="repository.owner"
                            class="mt-2 text-sm text-muted-foreground"
                        >
                            {{ repository.owner }}
                        </p>
                    </div>
                    <Badge
                        :class="getStatusColor(repository.status)"
                        variant="secondary"
                    >
                        {{ repository.status }}
                    </Badge>
                </div>

                <p
                    v-if="repository.summary"
                    class="mt-4 text-lg text-muted-foreground"
                >
                    {{ repository.summary }}
                </p>

                <div
                    class="mt-6 flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
                >
                    <Badge v-if="repository.language" variant="outline">
                        {{ repository.language }}
                    </Badge>
                    <span class="flex items-center gap-1">
                        <Star class="h-4 w-4" />
                        <span>{{ repository.stars }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <GitFork class="h-4 w-4" />
                        <span>{{ repository.forks }}</span>
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Button as-child size="sm">
                        <a
                            :href="repository.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="`View ${repository.name} on GitHub`"
                        >
                            <Github class="mr-1.5 h-4 w-4" />
                            View on GitHub
                            <ExternalLink class="ml-1 h-3 w-3" />
                        </a>
                    </Button>
                    <Button
                        v-if="repository.demo_url"
                        as-child
                        variant="outline"
                        size="sm"
                    >
                        <a
                            :href="repository.demo_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="`Open ${repository.name} live demo`"
                        >
                            Live Demo
                            <ExternalLink class="ml-1 h-3 w-3" />
                        </a>
                    </Button>
                </div>
            </header>

            <!-- Cover / first screenshot -->
            <div
                v-if="repository.thumbnail"
                class="mt-8 overflow-hidden rounded-xl"
            >
                <img
                    :src="thumbnailUrl(repository.thumbnail) ?? ''"
                    :alt="`${repository.name} preview`"
                    class="w-full"
                    fetchpriority="high"
                    decoding="async"
                />
            </div>

            <Separator class="my-10" />

            <!-- Description body (rich-text from Filament) -->
            <div
                v-if="repository.description"
                class="prose prose-neutral dark:prose-invert max-w-none"
                v-html="repository.description"
            />

            <!-- Tech stack -->
            <div v-if="repository.technologies?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Tech Stack</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <Badge
                        v-for="tech in repository.technologies"
                        :key="tech"
                        variant="secondary"
                    >
                        {{ tech }}
                    </Badge>
                </div>
            </div>

            <!-- Built for project -->
            <div v-if="repository.project" class="mt-10">
                <h2 class="text-2xl font-bold">Built for</h2>
                <Link
                    :href="`/projects/${repository.project.slug}`"
                    class="mt-4 block"
                >
                    <Card
                        class="transition-all hover:border-foreground/20 hover:shadow-md"
                    >
                        <CardHeader>
                            <CardTitle class="text-base">{{
                                repository.project.title
                            }}</CardTitle>
                        </CardHeader>
                        <CardContent v-if="repository.project.summary">
                            <p class="text-sm text-muted-foreground">
                                {{ repository.project.summary }}
                            </p>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Screenshots -->
            <div v-if="repository.screenshots?.length" class="mt-10">
                <h2 class="text-2xl font-bold">Screenshots</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="(src, idx) in repository.screenshots"
                        :key="idx"
                        class="overflow-hidden rounded-lg border border-border/50"
                    >
                        <img
                            :src="src"
                            :alt="`${repository.name} screenshot ${idx + 1}`"
                            class="w-full"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                </div>
            </div>

            <!-- Related articles -->
            <div v-if="relatedArticles.length" class="mt-12">
                <h2 class="text-2xl font-bold">Related reading</h2>
                <ul
                    class="mt-4 divide-y divide-border/50 rounded-xl border border-border/50"
                >
                    <li v-for="article in relatedArticles" :key="article.id">
                        <Link
                            :href="`/blog/${article.slug}`"
                            class="block p-4 transition-colors hover:bg-muted/40"
                        >
                            <p class="font-medium">{{ article.title }}</p>
                            <p
                                v-if="article.excerpt"
                                class="mt-1 line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ article.excerpt }}
                            </p>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </article>
</template>
