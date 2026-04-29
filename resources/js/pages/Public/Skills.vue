<script setup lang="ts">
import { computed } from 'vue';

interface SkillItem {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    proficiency_label: string | null;
    proficiency_score: number;
    years_experience: number | null;
    icon: string | null;
}

interface Props {
    skills: SkillItem[];
}

const props = defineProps<Props>();

function isUrl(icon: string | null): boolean {
    return !!icon && (icon.startsWith('http') || icon.startsWith('/'));
}

function getDeviconClass(icon: string): string | null {
    // Extract class from HTML tag like <i class="devicon-php-plain"></i>
    const match = icon.match(/class="([^"]+)"/);

    if (match) {
        return match[1];
    }

    // Already a class string like "devicon-php-plain"
    if (icon.startsWith('devicon-')) {
        return icon;
    }

    return null;
}

const grouped = computed(() => {
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
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">
                    Skills & Expertise
                </h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    A comprehensive overview of technologies, tools, and
                    methodologies I work with.
                </p>
            </div>

            <div
                v-for="(skills, category) in grouped"
                :key="category"
                class="mt-12"
            >
                <h2 class="text-xl font-semibold">{{ category }}</h2>
                <div
                    class="mt-4 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                >
                    <div
                        v-for="skill in skills"
                        :key="skill.id"
                        class="rounded-lg border border-border/40 p-4 transition-colors hover:bg-accent"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-xl"
                            >
                                <img
                                    v-if="isUrl(skill.icon)"
                                    :src="skill.icon ?? ''"
                                    :alt="skill.name"
                                    class="h-6 w-6 object-contain"
                                />
                                <i
                                    v-else-if="
                                        skill.icon &&
                                        getDeviconClass(skill.icon)
                                    "
                                    :class="getDeviconClass(skill.icon)"
                                />
                                <span v-else class="text-lg">🔧</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium">{{ skill.name }}</p>
                                <span
                                    v-if="skill.proficiency_label"
                                    class="text-xs text-muted-foreground"
                                    >{{ skill.proficiency_label }}</span
                                >
                            </div>
                        </div>
                        <!-- Proficiency score temporarily hidden
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full bg-primary transition-all"
                                :style="{ width: `${skill.proficiency_score}%` }"
                            />
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-muted-foreground">
                            <span>{{ skill.proficiency_score }}%</span>
                            <span v-if="skill.years_experience">{{ skill.years_experience }} yrs</span>
                        </div>
                        -->
                    </div>
                </div>
            </div>

            <div v-if="!skills.length" class="mt-14 text-center">
                <p class="text-lg font-medium">Skills coming soon</p>
            </div>
        </div>
    </section>
</template>
