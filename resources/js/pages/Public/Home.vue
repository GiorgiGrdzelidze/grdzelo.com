<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Code2, ExternalLink, Star } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    cover_image: string | null;
    tech_stack: string[] | null;
    year: number | null;
}

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image: string | null;
    publish_at: string;
    reading_time: number;
}

interface ServiceItem {
    id: number;
    title: string;
    slug: string;
    summary: string;
    icon: string | null;
}

interface SkillItem {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    proficiency_label: string | null;
    proficiency_score: number;
    icon: string | null;
}

interface TestimonialItem {
    id: number;
    author_name: string;
    author_role: string | null;
    company: string | null;
    quote: string;
    avatar: string | null;
    rating: number | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    featuredProjects: Project[];
    latestArticles: ArticleItem[];
    services: ServiceItem[];
    skills: SkillItem[];
    testimonials: TestimonialItem[];
}

const props = defineProps<Props>();

const groupedSkills = computed(() => {
    const groups: Record<string, SkillItem[]> = {};

    for (const skill of props.skills) {
        const cat = skill.category || 'Other';

        if (!groups[cat]) {
            groups[cat] = [];
        }

        groups[cat].push(skill);
    }

    return groups;
});

function isUrl(icon: string | null): boolean {
    return !!icon && (icon.startsWith('http') || icon.startsWith('/'));
}

function getDeviconClass(icon: string): string | null {
    const match = icon.match(/class="([^"]+)"/);

    if (match) {
        return match[1];
    }

    if (icon.startsWith('devicon-')) {
        return icon;
    }

    return null;
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-6xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    {{ settings?.tagline || 'Product-Minded Software Engineer' }}
                </h1>
                <p class="mt-6 text-lg leading-8 text-muted-foreground">
                    I build elegant, scalable web applications with a focus on clean architecture,
                    exceptional user experience, and measurable business impact.
                </p>
                <div class="mt-10 flex items-center justify-center gap-4">
                    <Button as-child size="lg">
                        <Link href="/projects">
                            View My Work
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                    <Button as-child variant="outline" size="lg">
                        <Link href="/contact">
                            {{ settings?.default_cta_text || "Let's Talk" }}
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45%_40%_at_50%_60%,hsl(var(--primary)/0.04),transparent)]" />
    </section>

    <!-- Featured Projects -->
    <section v-if="featuredProjects.length" class="border-t border-border/40 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Featured Projects</h2>
                    <p class="mt-2 text-muted-foreground">Selected work that I'm proud of</p>
                </div>
                <Button as-child variant="ghost" size="sm">
                    <Link href="/projects">
                        View All
                        <ArrowRight class="ml-1 h-4 w-4" />
                    </Link>
                </Button>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="project in featuredProjects"
                    :key="project.id"
                    :href="`/projects/${project.slug}`"
                    class="group"
                >
                    <Card class="h-full transition-all duration-200 hover:shadow-lg hover:border-foreground/20">
                        <div v-if="project.cover_image" class="aspect-video overflow-hidden rounded-t-lg">
                            <img
                                :src="`/storage/${project.cover_image}`"
                                :alt="project.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>
                        <div v-else class="flex aspect-video items-center justify-center rounded-t-lg bg-muted">
                            <Code2 class="h-10 w-10 text-muted-foreground" />
                        </div>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-lg group-hover:text-primary/80">{{ project.title }}</CardTitle>
                                <ExternalLink class="h-4 w-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="line-clamp-2 text-sm text-muted-foreground">{{ project.summary }}</p>
                            <div v-if="project.tech_stack?.length" class="mt-3 flex flex-wrap gap-1">
                                <Badge v-for="tech in project.tech_stack.slice(0, 4)" :key="tech" variant="secondary" class="text-xs">
                                    {{ tech }}
                                </Badge>
                                <Badge v-if="project.tech_stack.length > 4" variant="secondary" class="text-xs">
                                    +{{ project.tech_stack.length - 4 }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section v-if="services.length" class="border-t border-border/40 bg-muted/30 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight">What I Do</h2>
                <p class="mt-2 text-muted-foreground">Services I offer to help you build and grow</p>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="service in services" :key="service.id" class="text-center">
                    <CardHeader>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Code2 class="h-6 w-6" />
                        </div>
                        <CardTitle class="text-lg">{{ service.title }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">{{ service.summary }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>

    <!-- Skills -->
    <section v-if="props.skills.length" class="border-t border-border/40 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Skills & Expertise</h2>
                    <p class="mt-2 text-muted-foreground">Technologies and tools I work with daily</p>
                </div>
                <Button as-child variant="ghost" size="sm">
                    <Link href="/skills">
                        View All
                        <ArrowRight class="ml-1 h-4 w-4" />
                    </Link>
                </Button>
            </div>

            <div v-for="(skills, category) in groupedSkills" :key="category" class="mt-8">
                <h3 class="mb-4 text-lg font-semibold">{{ category }}</h3>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="skill in skills"
                        :key="skill.id"
                        class="flex items-center gap-3 rounded-lg border border-border/40 bg-background p-4 transition-colors hover:bg-accent"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-xl">
                            <img
                                v-if="isUrl(skill.icon)"
                                :src="skill.icon ?? ''"
                                :alt="skill.name"
                                class="h-6 w-6 object-contain"
                            />
                            <i v-else-if="skill.icon && getDeviconClass(skill.icon)" :class="getDeviconClass(skill.icon)" />
                            <span v-else class="text-lg">🔧</span>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ skill.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section v-if="testimonials.length" class="border-t border-border/40 bg-muted/30 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight">What People Say</h2>
                <p class="mt-2 text-muted-foreground">Feedback from clients and colleagues</p>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="testimonial in testimonials" :key="testimonial.id" class="flex flex-col">
                    <CardContent class="flex-1 pt-6">
                        <div v-if="testimonial.rating" class="mb-3 flex gap-0.5">
                            <Star
                                v-for="i in testimonial.rating"
                                :key="i"
                                class="h-4 w-4 fill-amber-400 text-amber-400"
                            />
                        </div>
                        <blockquote class="text-sm leading-relaxed text-muted-foreground">
                            "{{ testimonial.quote }}"
                        </blockquote>
                    </CardContent>
                    <div class="flex items-center gap-3 border-t border-border/40 px-6 py-4">
                        <div v-if="testimonial.avatar" class="h-10 w-10 overflow-hidden rounded-full">
                            <img :src="`/storage/${testimonial.avatar}`" :alt="testimonial.author_name" class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                            {{ testimonial.author_name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ testimonial.author_name }}</p>
                            <p v-if="testimonial.author_role || testimonial.company" class="text-xs text-muted-foreground">
                                {{ [testimonial.author_role, testimonial.company].filter(Boolean).join(' at ') }}
                            </p>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </section>

    <!-- Latest Articles -->
    <section v-if="latestArticles.length" class="border-t border-border/40 py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Latest Articles</h2>
                    <p class="mt-2 text-muted-foreground">Thoughts on engineering, product, and craft</p>
                </div>
                <Button as-child variant="ghost" size="sm">
                    <Link href="/blog">
                        View All
                        <ArrowRight class="ml-1 h-4 w-4" />
                    </Link>
                </Button>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="article in latestArticles"
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
                            <CardTitle class="text-lg group-hover:text-primary/80">{{ article.title }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="line-clamp-2 text-sm text-muted-foreground">{{ article.excerpt }}</p>
                            <div class="mt-3 flex items-center gap-3 text-xs text-muted-foreground">
                                <span>{{ formatDate(article.publish_at) }}</span>
                                <span>{{ article.reading_time }} min read</span>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="border-t border-border/40 bg-primary py-20 text-primary-foreground">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight">Ready to Build Something Great?</h2>
            <p class="mt-4 text-lg opacity-90">
                I'm always interested in hearing about new projects and opportunities.
            </p>
            <div class="mt-8 flex items-center justify-center gap-4">
                <Button as-child size="lg" variant="secondary">
                    <Link href="/contact">
                        {{ settings?.default_cta_text || "Let's Talk" }}
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </section>
</template>
