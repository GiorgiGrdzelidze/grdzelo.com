<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Code2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface ServiceItem {
    id: number;
    title: string;
    slug: string;
    summary: string;
    description: string | null;
    icon: string | null;
    cta_text: string | null;
    cta_url: string | null;
}

interface Props {
    services: ServiceItem[];
}

defineProps<Props>();
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Services</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    I help businesses and teams build high-quality web applications with clean architecture and exceptional user experience.
                </p>
            </div>

            <div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="service in services" :key="service.id" class="flex flex-col">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Code2 class="h-6 w-6" />
                        </div>
                        <CardTitle class="text-xl">{{ service.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-1 flex-col">
                        <p class="flex-1 text-sm text-muted-foreground leading-relaxed">{{ service.summary }}</p>
                        <div v-if="service.description" class="mt-4 prose prose-sm prose-neutral dark:prose-invert max-w-none" v-html="service.description" />
                        <div v-if="service.cta_url" class="mt-6">
                            <Button as-child variant="outline" size="sm">
                                <Link :href="service.cta_url">
                                    {{ service.cta_text || 'Learn More' }}
                                    <ArrowRight class="ml-1 h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-if="!services.length" class="mt-14 text-center">
                <Code2 class="mx-auto h-12 w-12 text-muted-foreground" />
                <p class="mt-4 text-lg font-medium">Services coming soon</p>
            </div>

            <!-- CTA -->
            <div class="mt-16 rounded-xl border border-border/40 bg-muted/30 p-8 text-center">
                <h2 class="text-2xl font-bold">Have a project in mind?</h2>
                <p class="mt-2 text-muted-foreground">Let's discuss how I can help you achieve your goals.</p>
                <Button as-child size="lg" class="mt-6">
                    <Link href="/contact">
                        Get in Touch
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </section>
</template>
