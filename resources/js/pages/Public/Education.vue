<script setup lang="ts">
import { GraduationCap, Calendar, MapPin } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface EducationItem {
    id: number;
    institution: string;
    degree: string | null;
    field_of_study: string | null;
    location: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
    achievements: string[] | null;
    logo: string | null;
    is_featured: boolean;
}

interface Props {
    education: EducationItem[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
    });
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Education</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Academic background and qualifications that shape my
                    expertise.
                </p>
            </div>

            <div class="mt-14 space-y-10">
                <div
                    v-for="edu in education"
                    :key="edu.id"
                    class="relative pl-8 before:absolute before:top-2 before:left-0 before:h-3 before:w-3 before:rounded-full before:bg-primary before:content-[''] after:absolute after:top-5 after:left-[5px] after:h-full after:w-0.5 after:bg-border/40 after:content-[''] last:after:hidden"
                >
                    <div class="flex items-start gap-4">
                        <div v-if="edu.logo" class="hidden shrink-0 sm:block">
                            <img
                                :src="`/storage/${edu.logo}`"
                                :alt="edu.institution"
                                class="h-12 w-12 rounded-lg border border-border/40 object-contain p-1"
                            />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-semibold">
                                    {{ edu.degree || 'Studies' }}
                                </h2>
                                <Badge
                                    v-if="edu.is_current"
                                    variant="default"
                                    class="text-xs"
                                    >Current</Badge
                                >
                                <Badge
                                    v-if="edu.is_featured"
                                    variant="secondary"
                                    class="text-xs"
                                    >Featured</Badge
                                >
                            </div>
                            <p
                                v-if="edu.field_of_study"
                                class="mt-0.5 text-sm font-medium text-muted-foreground"
                            >
                                {{ edu.field_of_study }}
                            </p>
                            <p
                                class="mt-1 flex items-center gap-2 text-sm text-muted-foreground"
                            >
                                <GraduationCap class="h-3.5 w-3.5" />
                                {{ edu.institution }}
                            </p>
                            <div
                                class="mt-1 flex items-center gap-3 text-xs text-muted-foreground"
                            >
                                <span class="flex items-center gap-1">
                                    <Calendar class="h-3 w-3" />
                                    {{ formatDate(edu.start_date) }} —
                                    {{
                                        edu.is_current
                                            ? 'Present'
                                            : edu.end_date
                                              ? formatDate(edu.end_date)
                                              : ''
                                    }}
                                </span>
                                <span
                                    v-if="edu.location"
                                    class="flex items-center gap-1"
                                >
                                    <MapPin class="h-3 w-3" />
                                    {{ edu.location }}
                                </span>
                            </div>
                            <p
                                v-if="edu.description"
                                class="mt-3 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ edu.description }}
                            </p>
                            <ul
                                v-if="edu.achievements?.length"
                                class="mt-3 space-y-1"
                            >
                                <li
                                    v-for="(a, i) in edu.achievements"
                                    :key="i"
                                    class="flex items-start gap-2 text-sm text-muted-foreground"
                                >
                                    <span
                                        class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-primary"
                                    />
                                    {{ a }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!education.length" class="mt-14 text-center">
                <p class="text-lg font-medium">Education details coming soon</p>
            </div>
        </div>
    </section>
</template>
