<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import {
    ArrowLeft,
    BookOpen,
    Check,
    Copy,
    ExternalLink,
    GitFork,
    Github,
    Star,
    Terminal,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { repositoryStatusClass } from '@/lib/repository-status';

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

interface InstallSnippet {
    tool: string;
    command: string;
    label: string;
}

interface Props {
    repository: Repository;
    relatedArticles: RelatedArticle[];
}

const props = defineProps<Props>();

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

function formatNumber(value: number): string {
    if (value >= 1000) {
        return `${(value / 1000).toFixed(value >= 10000 ? 0 : 1)}k`;
    }

    return value.toString();
}

const repoPath = computed<string | null>(() => {
    try {
        const url = new URL(props.repository.url);
        const path = url.pathname.replace(/^\/+|\/+$|\.git$/g, '');

        return path || null;
    } catch {
        return null;
    }
});

const installSnippets = computed<InstallSnippet[]>(() => {
    const lang = props.repository.language?.toLowerCase() ?? '';
    const techs = (props.repository.technologies ?? []).map((t) =>
        t.toLowerCase(),
    );
    const path = repoPath.value;
    const url = props.repository.url;

    if (!path) {
        return [];
    }

    const has = (...keys: string[]) =>
        keys.some((k) => lang.includes(k) || techs.includes(k));

    const pkgName = path.split('/').pop() ?? path;
    const cloneCmd = `git clone ${url}${url.endsWith('.git') ? '' : '.git'}`;

    if (has('php', 'laravel')) {
        return [
            {
                tool: 'composer',
                label: 'Composer',
                command: `composer require ${path}`,
            },
            { tool: 'git', label: 'Clone', command: cloneCmd },
        ];
    }

    if (has('typescript', 'javascript', 'ts', 'js', 'vue', 'react', 'node')) {
        return [
            { tool: 'npm', label: 'npm', command: `npm install ${pkgName}` },
            { tool: 'pnpm', label: 'pnpm', command: `pnpm add ${pkgName}` },
            { tool: 'git', label: 'Clone', command: cloneCmd },
        ];
    }

    if (has('rust')) {
        return [
            { tool: 'cargo', label: 'Cargo', command: `cargo add ${pkgName}` },
            { tool: 'git', label: 'Clone', command: cloneCmd },
        ];
    }

    if (has('go', 'golang')) {
        return [
            { tool: 'go', label: 'Go', command: `go get ${path}` },
            { tool: 'git', label: 'Clone', command: cloneCmd },
        ];
    }

    if (has('python', 'py')) {
        return [
            { tool: 'pip', label: 'pip', command: `pip install ${pkgName}` },
            { tool: 'git', label: 'Clone', command: cloneCmd },
        ];
    }

    return [{ tool: 'git', label: 'Clone', command: cloneCmd }];
});

const { copy, copied } = useClipboard({ legacy: true, copiedDuring: 1500 });
</script>

<template>
    <article class="relative pb-16">
        <!-- Decorative gradient backdrop -->
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[420px] overflow-hidden"
            aria-hidden="true"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"
            />
            <div
                class="absolute -top-32 left-1/2 h-72 w-[640px] -translate-x-1/2 rounded-full bg-primary/15 blur-3xl"
            />
        </div>

        <div class="mx-auto max-w-4xl px-4 pt-10 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <Link
                href="/repositories"
                class="group inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft
                    class="h-4 w-4 transition-transform group-hover:-translate-x-0.5"
                />
                Back to Repositories
            </Link>

            <!-- Hero -->
            <header class="mt-8">
                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                        :class="repositoryStatusClass(repository.status)"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-current"
                            aria-hidden="true"
                        />
                        {{ repository.status }}
                    </span>
                    <Badge
                        v-if="repository.language"
                        variant="outline"
                        class="text-xs font-medium"
                    >
                        {{ repository.language }}
                    </Badge>
                    <span
                        v-if="repository.is_featured"
                        class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary ring-1 ring-primary/20 ring-inset"
                    >
                        <Star class="h-3 w-3 fill-current" />
                        Featured
                    </span>
                </div>

                <h1 class="mt-5 text-4xl font-bold tracking-tight sm:text-5xl">
                    {{ repository.name }}
                </h1>

                <p
                    v-if="repoPath"
                    class="mt-3 font-mono text-sm text-muted-foreground"
                >
                    {{ repoPath }}
                </p>

                <p
                    v-if="repository.summary"
                    class="mt-5 max-w-2xl text-lg leading-relaxed text-muted-foreground"
                >
                    {{ repository.summary }}
                </p>

                <!-- Stats row -->
                <dl class="mt-8 flex flex-wrap items-center gap-3">
                    <div
                        class="inline-flex items-center gap-2 rounded-lg border border-border/60 bg-card/50 px-3 py-1.5 text-sm shadow-sm backdrop-blur"
                    >
                        <Star class="h-4 w-4 text-amber-500" />
                        <dt class="sr-only">Stars</dt>
                        <dd class="font-semibold tabular-nums">
                            {{ formatNumber(repository.stars) }}
                        </dd>
                        <span class="text-xs text-muted-foreground">stars</span>
                    </div>
                    <div
                        class="inline-flex items-center gap-2 rounded-lg border border-border/60 bg-card/50 px-3 py-1.5 text-sm shadow-sm backdrop-blur"
                    >
                        <GitFork class="h-4 w-4 text-muted-foreground" />
                        <dt class="sr-only">Forks</dt>
                        <dd class="font-semibold tabular-nums">
                            {{ formatNumber(repository.forks) }}
                        </dd>
                        <span class="text-xs text-muted-foreground">forks</span>
                    </div>
                </dl>

                <!-- CTAs -->
                <div class="mt-6 flex flex-wrap gap-2">
                    <Button as-child size="lg">
                        <a
                            :href="repository.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="`View ${repository.name} on GitHub`"
                        >
                            <Github class="mr-2 h-4 w-4" />
                            View on GitHub
                            <ExternalLink class="ml-2 h-3.5 w-3.5 opacity-70" />
                        </a>
                    </Button>
                    <Button
                        v-if="repository.demo_url"
                        as-child
                        variant="outline"
                        size="lg"
                    >
                        <a
                            :href="repository.demo_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="`Open ${repository.name} live demo`"
                        >
                            Live Demo
                            <ExternalLink class="ml-2 h-3.5 w-3.5 opacity-70" />
                        </a>
                    </Button>
                </div>
            </header>

            <!-- Cover -->
            <div
                v-if="repository.thumbnail"
                class="mt-12 aspect-video overflow-hidden rounded-2xl border border-border/60 bg-card/40 shadow-xl shadow-primary/5"
            >
                <img
                    :src="thumbnailUrl(repository.thumbnail) ?? ''"
                    :alt="`${repository.name} preview`"
                    class="h-full w-full object-cover"
                    fetchpriority="high"
                    decoding="async"
                />
            </div>

            <!-- Installation -->
            <section v-if="installSnippets.length" class="mt-14">
                <div class="flex items-center gap-2">
                    <Terminal class="h-5 w-5 text-primary" />
                    <h2 class="text-2xl font-bold tracking-tight">
                        Installation
                    </h2>
                </div>
                <p class="mt-2 text-sm text-muted-foreground">
                    Pick the package manager you prefer.
                </p>

                <div class="mt-5 space-y-3">
                    <div
                        v-for="snippet in installSnippets"
                        :key="snippet.tool"
                        class="group relative overflow-hidden rounded-xl border border-border/60 bg-zinc-950 text-zinc-100 shadow-md ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10"
                    >
                        <div
                            class="flex items-center justify-between border-b border-white/5 bg-white/[0.02] px-4 py-2 text-xs"
                        >
                            <span
                                class="inline-flex items-center gap-1.5 font-medium tracking-wider text-zinc-300 uppercase"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-emerald-400/80"
                                    aria-hidden="true"
                                />
                                {{ snippet.label }}
                            </span>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-zinc-300 transition-colors hover:bg-white/5 hover:text-zinc-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                                :aria-label="`Copy ${snippet.label} install command`"
                                @click="copy(snippet.command)"
                            >
                                <Check
                                    v-if="copied"
                                    class="h-3.5 w-3.5 text-emerald-400"
                                />
                                <Copy v-else class="h-3.5 w-3.5" />
                                {{ copied ? 'Copied' : 'Copy' }}
                            </button>
                        </div>
                        <pre
                            class="overflow-x-auto px-4 py-4 font-mono text-sm leading-relaxed"
                        ><code><span class="select-none text-emerald-400">$</span> {{ snippet.command }}</code></pre>
                    </div>
                </div>
            </section>

            <!-- Description body -->
            <section v-if="repository.description" class="mt-14">
                <div class="flex items-center gap-2">
                    <BookOpen class="h-5 w-5 text-primary" />
                    <h2 class="text-2xl font-bold tracking-tight">About</h2>
                </div>
                <div
                    class="prose prose-neutral dark:prose-invert prose-headings:scroll-mt-24 prose-headings:font-bold prose-h2:mt-10 prose-h2:text-2xl prose-h3:text-xl prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-code:rounded prose-code:border prose-code:border-border/60 prose-code:bg-muted prose-code:px-1.5 prose-code:py-0.5 prose-code:text-[0.875em] prose-code:font-medium prose-code:before:content-none prose-code:after:content-none prose-pre:overflow-x-auto prose-pre:rounded-xl prose-pre:border prose-pre:border-border/60 prose-pre:bg-zinc-950 prose-pre:p-4 prose-pre:text-zinc-100 prose-pre:shadow-md dark:prose-pre:bg-zinc-900 mt-5 max-w-none"
                    v-html="repository.description"
                />
            </section>

            <!-- Tech stack -->
            <section v-if="repository.technologies?.length" class="mt-14">
                <h2 class="text-2xl font-bold tracking-tight">Tech Stack</h2>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span
                        v-for="tech in repository.technologies"
                        :key="tech"
                        class="inline-flex items-center rounded-md border border-border/60 bg-card/60 px-2.5 py-1 text-sm font-medium text-foreground/80 transition-colors hover:border-primary/40 hover:text-foreground"
                    >
                        {{ tech }}
                    </span>
                </div>
            </section>

            <!-- Built for project -->
            <section v-if="repository.project" class="mt-14">
                <h2 class="text-2xl font-bold tracking-tight">Built for</h2>
                <Link
                    :href="`/projects/${repository.project.slug}`"
                    class="group mt-5 block overflow-hidden rounded-xl border border-border/60 bg-card/50 p-5 transition-all hover:-translate-y-0.5 hover:border-primary/40 hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p
                                class="text-base font-semibold transition-colors group-hover:text-primary"
                            >
                                {{ repository.project.title }}
                            </p>
                            <p
                                v-if="repository.project.summary"
                                class="mt-2 line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ repository.project.summary }}
                            </p>
                        </div>
                        <ExternalLink
                            class="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary"
                        />
                    </div>
                </Link>
            </section>

            <!-- Screenshots -->
            <section v-if="repository.screenshots?.length" class="mt-14">
                <h2 class="text-2xl font-bold tracking-tight">Screenshots</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="(src, idx) in repository.screenshots"
                        :key="idx"
                        class="aspect-video overflow-hidden rounded-xl border border-border/60 bg-card/40 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <img
                            :src="src"
                            :alt="`${repository.name} screenshot ${idx + 1}`"
                            class="h-full w-full object-cover"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                </div>
            </section>

            <!-- Related articles -->
            <section v-if="relatedArticles.length" class="mt-14">
                <h2 class="text-2xl font-bold tracking-tight">
                    Related reading
                </h2>
                <ul
                    class="mt-5 divide-y divide-border/50 overflow-hidden rounded-xl border border-border/60 bg-card/40"
                >
                    <li v-for="article in relatedArticles" :key="article.id">
                        <Link
                            :href="`/blog/${article.slug}`"
                            class="group flex items-start justify-between gap-4 p-5 transition-colors hover:bg-muted/40"
                        >
                            <div class="min-w-0">
                                <p
                                    class="font-medium transition-colors group-hover:text-primary"
                                >
                                    {{ article.title }}
                                </p>
                                <p
                                    v-if="article.excerpt"
                                    class="mt-1 line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{ article.excerpt }}
                                </p>
                            </div>
                            <ArrowLeft
                                class="mt-1 h-4 w-4 shrink-0 rotate-[135deg] text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary"
                            />
                        </Link>
                    </li>
                </ul>
            </section>
        </div>
    </article>
</template>
