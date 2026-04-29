<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Clock, User } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

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
    article: ArticleDetail;
    relatedArticles: RelatedArticle[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
</script>

<template>
    <article class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <Link
                href="/blog"
                class="mb-8 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Blog
            </Link>

            <!-- Header -->
            <header>
                <div class="flex items-center gap-2">
                    <Badge v-if="article.category" variant="secondary">{{
                        article.category.name
                    }}</Badge>
                </div>
                <h1 class="mt-3 text-4xl font-bold tracking-tight">
                    {{ article.title }}
                </h1>
                <p
                    v-if="article.excerpt"
                    class="mt-4 text-lg text-muted-foreground"
                >
                    {{ article.excerpt }}
                </p>

                <div
                    class="mt-6 flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
                >
                    <span v-if="article.author" class="flex items-center gap-1">
                        <User class="h-4 w-4" />
                        {{ article.author.name }}
                    </span>
                    <span class="flex items-center gap-1">
                        <Calendar class="h-4 w-4" />
                        {{ formatDate(article.publish_at) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <Clock class="h-4 w-4" />
                        {{ article.reading_time }} min read
                    </span>
                </div>
            </header>

            <!-- Cover Image -->
            <div
                v-if="article.cover_image"
                class="mt-8 overflow-hidden rounded-xl"
            >
                <img
                    :src="`/storage/${article.cover_image}`"
                    :alt="article.title"
                    class="w-full"
                />
            </div>

            <Separator class="my-8" />

            <!-- Body -->
            <div
                class="prose prose-neutral dark:prose-invert max-w-none"
                v-html="article.body"
            />

            <!-- Tags -->
            <div v-if="article.tags?.length" class="mt-8 flex flex-wrap gap-2">
                <Badge
                    v-for="tag in article.tags"
                    :key="tag.id"
                    variant="outline"
                    >{{ tag.name }}</Badge
                >
            </div>
        </div>

        <!-- Related Articles -->
        <div
            v-if="relatedArticles.length"
            class="mt-16 border-t border-border/40 py-16"
        >
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold">Related Articles</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    <Link
                        v-for="ra in relatedArticles"
                        :key="ra.id"
                        :href="`/blog/${ra.slug}`"
                        class="group"
                    >
                        <Card
                            class="h-full transition-all hover:border-foreground/20 hover:shadow-md"
                        >
                            <CardHeader>
                                <CardTitle
                                    class="text-base group-hover:text-primary/80"
                                    >{{ ra.title }}</CardTitle
                                >
                            </CardHeader>
                            <CardContent>
                                <p
                                    class="line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{ ra.excerpt }}
                                </p>
                                <div
                                    class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                                >
                                    <span>{{ formatDate(ra.publish_at) }}</span>
                                    <span>{{ ra.reading_time }} min read</span>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </article>
</template>
