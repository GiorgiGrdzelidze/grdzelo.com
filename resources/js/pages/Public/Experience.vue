<script setup lang="ts">
import { Briefcase, Calendar } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface ExperienceItem {
    id: number;
    company: string;
    role: string;
    type: string;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    summary: string | null;
    achievements: string[] | null;
    technologies: string[] | null;
    website_url: string | null;
    logo: string | null;
}

interface Props {
    experiences: ExperienceItem[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Experience</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    My professional journey through roles, projects, and milestones.
                </p>
            </div>

            <div class="mt-14 space-y-10">
                <div
                    v-for="exp in experiences"
                    :key="exp.id"
                    class="relative pl-8 before:absolute before:left-0 before:top-2 before:h-3 before:w-3 before:rounded-full before:bg-primary before:content-[''] after:absolute after:left-[5px] after:top-5 after:h-full after:w-0.5 after:bg-border/40 after:content-[''] last:after:hidden"
                >
                    <div class="flex items-start gap-4">
                        <div v-if="exp.logo" class="hidden shrink-0 sm:block">
                            <img :src="`/storage/${exp.logo}`" :alt="exp.company" class="h-12 w-12 rounded-lg object-contain border border-border/40 p-1" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-semibold">{{ exp.role }}</h2>
                                <Badge v-if="exp.is_current" variant="default" class="text-xs">Current</Badge>
                                <Badge variant="outline" class="text-xs">{{ exp.type }}</Badge>
                            </div>
                            <p class="mt-1 flex items-center gap-2 text-sm text-muted-foreground">
                                <Briefcase class="h-3.5 w-3.5" />
                                <a v-if="exp.website_url" :href="exp.website_url" target="_blank" rel="noopener noreferrer" class="hover:text-foreground underline">
                                    {{ exp.company }}
                                </a>
                                <span v-else>{{ exp.company }}</span>
                            </p>
                            <p class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                <Calendar class="h-3 w-3" />
                                {{ formatDate(exp.start_date) }} — {{ exp.is_current ? 'Present' : (exp.end_date ? formatDate(exp.end_date) : '') }}
                            </p>
                            <p v-if="exp.summary" class="mt-3 text-sm text-muted-foreground leading-relaxed">{{ exp.summary }}</p>
                            <ul v-if="exp.achievements?.length" class="mt-3 space-y-1">
                                <li v-for="(a, i) in exp.achievements" :key="i" class="flex items-start gap-2 text-sm text-muted-foreground">
                                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                                    {{ a }}
                                </li>
                            </ul>
                            <div v-if="exp.technologies?.length" class="mt-3 flex flex-wrap gap-1">
                                <Badge v-for="tech in exp.technologies" :key="tech" variant="secondary" class="text-xs">{{ tech }}</Badge>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!experiences.length" class="mt-14 text-center">
                <p class="text-lg font-medium">Experience details coming soon</p>
            </div>
        </div>
    </section>
</template>
