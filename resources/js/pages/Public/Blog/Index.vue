<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, BookOpen } from 'lucide-vue-next';
import { computed } from 'vue';
import { useLocalePath } from '@/composables/useLocalePath';
import { useT } from '@/composables/useTranslate';

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image: string | null;
    publish_at: string;
    reading_time: number;
    is_featured: boolean;
    category?: { id: number; name: string; slug: string } | null;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    articles_count: number;
}

interface PaginatedArticles {
    data: ArticleItem[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    articles: PaginatedArticles;
    categories: Category[];
    featured: ArticleItem[];
    activeCategory?: string | null;
}

const props = defineProps<Props>();

const { t, locale } = useT();
const localePath = useLocalePath();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString(locale.value || 'en', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

const totalArticles = computed(() => props.articles.total);
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.journal.eyebrow') }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('blog.title') }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('blog.lead') }}
            </p>
        </div>
    </section>

    <!-- ============ FEATURED ============ -->
    <section
        v-if="featured.length"
        class="border-t border-border px-6 pt-8 pb-10 sm:px-8 sm:pt-10 sm:pb-12 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-2 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ t('blog.featured') }}
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(featured.length) }}
                </span>
            </div>

            <div
                class="grid grid-cols-1 gap-px border border-border bg-border md:grid-cols-3"
            >
                <Link
                    v-for="(article, i) in featured"
                    :key="article.id"
                    :href="localePath(`/blog/${article.slug}`)"
                    class="group flex flex-col bg-background p-5 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                        <span>{{ article.reading_time }} min</span>
                    </div>

                    <div
                        v-if="article.cover_image"
                        class="mt-3 aspect-[4/3] overflow-hidden border border-border bg-muted/40"
                    >
                        <img
                            :src="`/storage/${article.cover_image}`"
                            :alt="article.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                    </div>

                    <h2
                        class="mt-3 inline-flex items-start gap-1 text-base font-semibold tracking-[-0.01em] text-foreground sm:text-lg"
                    >
                        {{ article.title }}
                        <ArrowUpRight
                            class="mt-1 h-3.5 w-3.5 shrink-0 text-muted-foreground transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-accent"
                            aria-hidden="true"
                        />
                    </h2>

                    <p
                        class="mt-2 line-clamp-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ article.excerpt }}
                    </p>

                    <div
                        class="mt-auto pt-3 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ formatDate(article.publish_at) }}
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <!-- ============ CATEGORIES ============ -->
    <section
        v-if="categories.length"
        class="border-t border-border px-6 pt-12 pb-6 sm:px-8 sm:pt-16 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    Filter
                </span>
                <Link
                    href="/blog"
                    preserve-scroll
                    preserve-state
                    :only="['articles', 'activeCategory']"
                    class="font-mono text-[11px] font-medium tracking-[0.12em] uppercase transition-colors"
                    :class="
                        !activeCategory
                            ? 'text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    All
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/blog?category=${cat.slug}`"
                    preserve-scroll
                    preserve-state
                    :only="['articles', 'activeCategory']"
                    class="font-mono text-[11px] font-medium tracking-[0.12em] uppercase transition-colors"
                    :class="
                        activeCategory === cat.slug
                            ? 'text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    {{ cat.name }}
                    <span class="text-muted-foreground/60"
                        >({{ cat.articles_count }})</span
                    >
                </Link>
            </div>
        </div>
    </section>

    <!-- ============ ARTICLES LIST ============ -->
    <section
        v-if="articles.data.length"
        class="border-t border-border px-6 pt-8 pb-24 sm:px-8 sm:pt-10 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-4 flex items-end justify-between gap-4">
                <span
                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                >
                    All articles
                </span>
                <span
                    class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                >
                    {{ pad(totalArticles) }}
                </span>
            </div>

            <ul class="border-t border-border">
                <li
                    v-for="(article, i) in articles.data"
                    :key="article.id"
                    class="group/row relative grid grid-cols-1 items-start gap-2 border-b border-border py-5 transition-colors hover:bg-muted/30 sm:grid-cols-[32px_minmax(0,1.6fr)_minmax(0,1fr)_auto] sm:items-center sm:gap-6"
                >
                    <Link
                        :href="localePath(`/blog/${article.slug}`)"
                        class="absolute inset-0 z-0"
                        :aria-label="`Read ${article.title}`"
                    />
                    <span
                        class="pointer-events-none relative z-10 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ pad(i + 1) }}
                    </span>

                    <div class="pointer-events-none relative z-10 min-w-0">
                        <div
                            class="inline-flex items-start gap-1 text-base font-semibold tracking-[-0.01em] text-foreground"
                        >
                            {{ article.title }}
                            <ArrowUpRight
                                class="mt-1 h-3.5 w-3.5 shrink-0 text-muted-foreground transition-all group-hover/row:translate-x-0.5 group-hover/row:-translate-y-0.5 group-hover/row:text-accent"
                                aria-hidden="true"
                            />
                        </div>
                        <p
                            v-if="article.excerpt"
                            class="mt-1 line-clamp-2 max-w-[60ch] text-sm leading-relaxed text-pretty text-muted-foreground"
                        >
                            {{ article.excerpt }}
                        </p>
                    </div>

                    <div
                        class="pointer-events-none relative z-10 flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px] tracking-[0.08em] text-muted-foreground uppercase"
                    >
                        <span v-if="article.category" class="text-foreground">
                            {{ article.category.name }}
                        </span>
                        <span>{{ article.reading_time }} min</span>
                    </div>

                    <span
                        class="pointer-events-none relative z-10 justify-self-start font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase sm:justify-self-end"
                    >
                        {{ formatDate(article.publish_at) }}
                    </span>
                </li>
            </ul>

            <!-- Pagination -->
            <nav
                v-if="articles.last_page > 1"
                class="mt-12 flex flex-wrap items-center justify-center gap-2"
                :aria-label="'Pagination'"
            >
                <template v-for="link in articles.links" :key="link.label">
                    <!-- eslint-disable vue/no-v-text-v-html-on-component -->
                    <component
                        :is="link.url ? Link : 'span'"
                        :href="link.url ?? undefined"
                        :preserve-scroll="link.url ? true : undefined"
                        :only="
                            link.url
                                ? ['articles', 'activeCategory']
                                : undefined
                        "
                        class="inline-flex h-9 min-w-9 items-center justify-center border px-3 font-mono text-[11px] font-medium tracking-[0.12em] uppercase transition-colors"
                        :class="[
                            link.active
                                ? 'border-foreground bg-foreground text-background'
                                : 'border-border text-foreground hover:border-foreground',
                            !link.url
                                ? 'cursor-default text-muted-foreground/40'
                                : '',
                        ]"
                        v-html="link.label"
                    />
                    <!-- eslint-enable vue/no-v-text-v-html-on-component -->
                </template>
            </nav>
        </div>
    </section>

    <!-- ============ EMPTY STATE ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <BookOpen
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
                aria-hidden="true"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                No articles yet
            </p>
        </div>
    </section>
</template>
