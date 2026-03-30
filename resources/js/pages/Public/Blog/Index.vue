<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Clock } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

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
}

interface Props {
    articles: PaginatedArticles;
    categories: Category[];
    featured: ArticleItem[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Blog</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Thoughts on engineering, product design, architecture, and the craft of building software.
                </p>
            </div>

            <!-- Featured Articles -->
            <div v-if="featured.length" class="mt-12 grid gap-6 lg:grid-cols-3">
                <Link
                    v-for="article in featured"
                    :key="article.id"
                    :href="`/blog/${article.slug}`"
                    class="group"
                >
                    <Card class="h-full transition-all duration-200 hover:shadow-lg hover:border-foreground/20">
                        <div v-if="article.cover_image" class="aspect-video overflow-hidden rounded-t-lg">
                            <img
                                :src="`/storage/${article.cover_image}`"
                                :alt="article.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <CardHeader>
                            <Badge variant="secondary" class="w-fit text-xs">Featured</Badge>
                            <CardTitle class="text-lg group-hover:text-primary/80">{{ article.title }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="line-clamp-2 text-sm text-muted-foreground">{{ article.excerpt }}</p>
                            <div class="mt-3 flex items-center gap-3 text-xs text-muted-foreground">
                                <span>{{ formatDate(article.publish_at) }}</span>
                                <span class="flex items-center gap-1">
                                    <Clock class="h-3 w-3" />
                                    {{ article.reading_time }} min
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Categories -->
            <div v-if="categories.length" class="mt-12 flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground">Filter:</span>
                <Badge variant="outline" class="cursor-pointer hover:bg-accent">All</Badge>
                <Badge
                    v-for="cat in categories"
                    :key="cat.id"
                    variant="outline"
                    class="cursor-pointer hover:bg-accent"
                >
                    {{ cat.name }} ({{ cat.articles_count }})
                </Badge>
            </div>

            <!-- Articles Grid -->
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="article in articles.data"
                    :key="article.id"
                    :href="`/blog/${article.slug}`"
                    class="group"
                >
                    <Card class="h-full transition-all duration-200 hover:shadow-md hover:border-foreground/20">
                        <div v-if="article.cover_image" class="aspect-video overflow-hidden rounded-t-lg">
                            <img
                                :src="`/storage/${article.cover_image}`"
                                :alt="article.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <CardHeader>
                            <Badge v-if="article.category" variant="secondary" class="w-fit text-xs">
                                {{ article.category.name }}
                            </Badge>
                            <CardTitle class="text-base group-hover:text-primary/80">{{ article.title }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="line-clamp-2 text-sm text-muted-foreground">{{ article.excerpt }}</p>
                            <div class="mt-3 flex items-center gap-3 text-xs text-muted-foreground">
                                <span>{{ formatDate(article.publish_at) }}</span>
                                <span class="flex items-center gap-1">
                                    <Clock class="h-3 w-3" />
                                    {{ article.reading_time }} min
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="!articles.data.length" class="mt-14 text-center">
                <BookOpen class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">No articles yet</p>
                <p class="mt-1 text-muted-foreground">Check back soon for new content.</p>
            </div>

            <!-- Pagination -->
            <nav v-if="articles.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
                <template v-for="link in articles.links" :key="link.label">
                    <!-- eslint-disable vue/no-v-text-v-html-on-component -->
                    <component
                        :is="link.url ? Link : 'span'"
                        :href="link.url ?? undefined"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm"
                        :class="[
                            link.active ? 'border-primary bg-primary text-primary-foreground' : 'border-border hover:bg-accent',
                            !link.url ? 'cursor-default opacity-50' : '',
                        ]"
                        v-html="link.label"
                    />
                    <!-- eslint-enable vue/no-v-text-v-html-on-component -->
                </template>
            </nav>
        </div>
    </section>
</template>
