<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowUpRight } from 'lucide-vue-next';
import { useT } from '@/composables/useTranslate';

interface ArticleDetail {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    body: string;
    cover_image: string | null;
    publish_at: string;
    reading_time: number;
    category: { id: number; name: string; slug: string } | null;
    author: { id: number; name: string } | null;
    tags: Array<{ id: number; name: string }>;
}

interface RelatedArticle {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image: string | null;
    publish_at: string;
    reading_time: number;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    article: ArticleDetail;
    relatedArticles: RelatedArticle[];
}

defineProps<Props>();

const { t, locale } = useT();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString(locale.value || 'en', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
}
</script>

<style scoped>
.article-body :deep(p) {
    line-height: 1.75;
    color: hsl(var(--foreground) / 0.88);
    text-wrap: pretty;
}

.article-body :deep(p:first-of-type) {
    font-size: 1.125rem;
    line-height: 1.7;
    color: hsl(var(--foreground));
}

.article-body :deep(h2) {
    margin-top: 3.5rem;
    padding-top: 2rem;
    border-top: 1px solid hsl(var(--border));
    font-size: 1.5rem;
    line-height: 1.25;
    letter-spacing: -0.02em;
    font-weight: 600;
}

.article-body :deep(h2::after) {
    content: '.';
    color: hsl(var(--accent));
}

.article-body :deep(h3) {
    margin-top: 2.5rem;
    font-size: 1.125rem;
    letter-spacing: -0.015em;
    font-weight: 600;
}

.article-body :deep(a) {
    color: hsl(var(--foreground));
    text-decoration: underline;
    text-decoration-thickness: 1px;
    text-underline-offset: 4px;
    transition: color 0.15s ease;
}

.article-body :deep(a:hover) {
    color: hsl(var(--accent));
}

.article-body :deep(:not(pre) > code) {
    font-family:
        ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.85em;
    padding: 0.1em 0.4em;
    border: 1px solid hsl(var(--border));
    background: hsl(var(--muted) / 0.4);
    color: hsl(var(--foreground));
    border-radius: 0;
}

.article-body :deep(:not(pre) > code::before),
.article-body :deep(:not(pre) > code::after) {
    content: none;
}

.article-body :deep(pre) {
    margin: 2rem 0;
    padding: 1.25rem 1.5rem;
    border: 1px solid hsl(var(--border));
    background: hsl(var(--muted) / 0.4);
    color: hsl(var(--foreground));
    font-size: 0.8125rem;
    line-height: 1.7;
    overflow-x: auto;
    border-radius: 0;
}

.article-body :deep(pre code) {
    background: transparent;
    padding: 0;
    border: 0;
    font-size: inherit;
    color: inherit;
}

.article-body :deep(blockquote) {
    margin: 2rem 0;
    padding-left: 1.25rem;
    border-left: 2px solid hsl(var(--accent));
    font-style: normal;
    color: hsl(var(--muted-foreground));
    quotes: none;
}

.article-body :deep(blockquote p::before),
.article-body :deep(blockquote p::after) {
    content: none;
}

.article-body :deep(ul),
.article-body :deep(ol) {
    margin: 1.25rem 0;
    padding-left: 1.5rem;
}

.article-body :deep(ul) {
    list-style: none;
}

.article-body :deep(ul > li) {
    position: relative;
    padding-left: 0.25rem;
    margin: 0.5rem 0;
    line-height: 1.7;
}

.article-body :deep(ul > li::before) {
    content: '';
    position: absolute;
    left: -1.25rem;
    top: 0.7em;
    width: 0.5rem;
    height: 1px;
    background: hsl(var(--accent));
}

.article-body :deep(ol) {
    counter-reset: list-counter;
    list-style: none;
}

.article-body :deep(ol > li) {
    counter-increment: list-counter;
    position: relative;
    padding-left: 0.25rem;
    margin: 0.5rem 0;
    line-height: 1.7;
}

.article-body :deep(ol > li::before) {
    content: counter(list-counter, decimal-leading-zero);
    position: absolute;
    left: -2rem;
    top: 0.05em;
    font-family:
        ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.7rem;
    letter-spacing: 0.12em;
    color: hsl(var(--muted-foreground));
}

.article-body :deep(hr) {
    margin: 3rem 0;
    border: 0;
    border-top: 1px solid hsl(var(--border));
}

.article-body :deep(img) {
    margin: 2rem 0;
    border: 1px solid hsl(var(--border));
    background: hsl(var(--muted) / 0.4);
    border-radius: 0;
}

.article-body :deep(strong) {
    color: hsl(var(--foreground));
    font-weight: 600;
}

.article-body :deep(table) {
    margin: 2rem 0;
    border-collapse: collapse;
    width: 100%;
    font-size: 0.875rem;
}

.article-body :deep(table th),
.article-body :deep(table td) {
    border: 1px solid hsl(var(--border));
    padding: 0.5rem 0.75rem;
    text-align: left;
}

.article-body :deep(table th) {
    background: hsl(var(--muted) / 0.4);
    font-family:
        ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.7rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: hsl(var(--muted-foreground));
    font-weight: 500;
}
</style>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-20 pb-10 sm:px-8 sm:pt-28 sm:pb-12 lg:px-12">
        <div class="mx-auto max-w-[760px]">
            <Link
                href="/blog"
                class="group inline-flex items-center gap-1.5 font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase transition-colors hover:text-foreground"
            >
                <ArrowLeft
                    class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5"
                    aria-hidden="true"
                />
                {{ t('blog.back') || 'Back to journal' }}
            </Link>

            <div class="mt-10">
                <span
                    v-if="article.category"
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ article.category.name }}
                </span>

                <h1
                    class="mt-4 text-[clamp(2rem,4.5vw,3.25rem)] leading-[1.08] font-semibold tracking-[-0.025em] text-balance"
                >
                    {{ article.title }}<span class="text-accent">.</span>
                </h1>

                <p
                    v-if="article.excerpt"
                    class="mt-6 text-lg leading-relaxed text-pretty text-muted-foreground"
                >
                    {{ article.excerpt }}
                </p>

                <div
                    class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-border pt-6 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    <span v-if="article.author" class="text-foreground">
                        {{ article.author.name }}
                    </span>
                    <span>{{ formatDate(article.publish_at) }}</span>
                    <span>{{ article.reading_time }} min read</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ COVER ============ -->
    <section
        v-if="article.cover_image"
        class="px-6 pb-12 sm:px-8 sm:pb-16 lg:px-12"
    >
        <div class="mx-auto max-w-[1100px]">
            <div
                class="aspect-[16/9] overflow-hidden border border-border bg-muted/40"
            >
                <img
                    :src="`/storage/${article.cover_image}`"
                    :alt="article.title"
                    class="h-full w-full object-cover"
                    fetchpriority="high"
                />
            </div>
        </div>
    </section>

    <!-- ============ BODY ============ -->
    <section
        class="border-t border-border px-6 pt-12 pb-16 sm:px-8 sm:pt-16 sm:pb-20 lg:px-12"
    >
        <div class="mx-auto max-w-[760px]">
            <div
                class="article-body prose max-w-none prose-neutral dark:prose-invert"
                v-html="article.body"
            />

            <div
                v-if="article.tags?.length"
                class="mt-12 flex flex-wrap gap-1.5 border-t border-border pt-8"
            >
                <span
                    v-for="tag in article.tags"
                    :key="tag.id"
                    class="border border-border px-2 py-0.5 font-mono text-[10px] font-medium tracking-[0.08em] text-muted-foreground uppercase"
                >
                    {{ tag.name }}
                </span>
            </div>
        </div>
    </section>

    <!-- ============ RELATED ============ -->
    <section
        v-if="relatedArticles.length"
        class="border-t border-border px-6 pt-10 pb-24 sm:px-8 sm:pt-12 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('blog.related') || 'More from the journal' }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(relatedArticles.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-1 gap-px border border-border bg-border md:grid-cols-3"
            >
                <Link
                    v-for="(ra, i) in relatedArticles"
                    :key="ra.id"
                    :href="`/blog/${ra.slug}`"
                    class="group flex flex-col bg-background p-5 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                        <span>{{ ra.reading_time }} min</span>
                    </div>

                    <div
                        v-if="ra.cover_image"
                        class="mt-3 aspect-[4/3] overflow-hidden border border-border bg-muted/40"
                    >
                        <img
                            :src="`/storage/${ra.cover_image}`"
                            :alt="ra.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                    </div>

                    <h3
                        class="mt-3 inline-flex items-start gap-1 text-base font-semibold tracking-[-0.01em] text-foreground sm:text-lg"
                    >
                        {{ ra.title }}
                        <ArrowUpRight
                            class="mt-1 h-3.5 w-3.5 shrink-0 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            aria-hidden="true"
                        />
                    </h3>

                    <p
                        v-if="ra.excerpt"
                        class="mt-2 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ ra.excerpt }}
                    </p>

                    <div
                        class="mt-auto pt-3 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ formatDate(ra.publish_at) }}
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>
