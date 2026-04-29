<script setup lang="ts">
import { ExternalLink } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';

interface SocialLinkItem {
    id: number;
    platform: string;
    label: string | null;
    url: string;
    username: string | null;
    is_highlighted: boolean;
}

interface Props {
    socialLinks: SocialLinkItem[];
}

defineProps<Props>();
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Social</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Find me around the web. Let's connect!
                </p>
            </div>

            <div class="mt-14 grid gap-4 sm:grid-cols-2">
                <a
                    v-for="link in socialLinks"
                    :key="link.id"
                    :href="link.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group"
                >
                    <Card
                        class="transition-all hover:border-foreground/20 hover:shadow-md"
                    >
                        <CardContent class="flex items-center gap-4 py-5">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                            >
                                <ExternalLink class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium capitalize">
                                        {{ link.label || link.platform }}
                                    </p>
                                    <Badge
                                        v-if="link.is_highlighted"
                                        variant="secondary"
                                        class="text-xs"
                                        >Featured</Badge
                                    >
                                </div>
                                <p
                                    v-if="link.username"
                                    class="truncate text-sm text-muted-foreground"
                                >
                                    @{{ link.username }}
                                </p>
                            </div>
                            <ExternalLink
                                class="h-4 w-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100"
                            />
                        </CardContent>
                    </Card>
                </a>
            </div>

            <div v-if="!socialLinks.length" class="mt-14 text-center">
                <p class="text-lg font-medium">Social links coming soon</p>
            </div>
        </div>
    </section>
</template>
