<script setup lang="ts">
import { Code2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { useT } from '@/composables/useTranslate';

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
    settings?: Record<string, any>;
    seo?: Record<string, any>;
    skills: SkillItem[];
}

const props = defineProps<Props>();

const { t } = useT();

function isUrl(icon: string | null): boolean {
    return !!icon && (icon.startsWith('http') || icon.startsWith('/'));
}

function getDeviconClass(icon: string): string | null {
    const trimmed = icon.trim();
    let raw: string | null = null;

    const classAttr = trimmed.match(/class\s*=\s*["']([^"']+)["']/i);

    if (classAttr) {
        raw = classAttr[1];
    } else {
        const inline = trimmed.match(/devicon-[\w-]+(?:\s+[\w-]+)*/i);

        if (inline) {
            raw = inline[0];
        }
    }

    if (!raw) {
        return null;
    }

    const tokens = raw.split(/\s+/).filter(Boolean);

    if (!tokens.some((tok) => tok.toLowerCase().startsWith('devicon-'))) {
        return null;
    }

    if (!tokens.includes('colored')) {
        tokens.push('colored');
    }

    return tokens.join(' ');
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
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

const totalSkills = computed(() => props.skills.length);
const totalCategories = computed(() => Object.keys(grouped.value).length);
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{
                t('sections.stack.eyebrow') || 'Stack'
            }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('skills.title') || 'Skills & expertise'
                }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{
                    t('skills.lead') ||
                    'A working catalogue of the languages, frameworks, and tools I reach for — grouped by where they sit in the stack.'
                }}
            </p>

            <div
                v-if="totalSkills"
                class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-border pt-6 font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
            >
                <span>{{ pad(totalSkills) }} skills</span>
                <span>{{ pad(totalCategories) }} categories</span>
            </div>
        </div>
    </section>

    <!-- ============ GROUPS ============ -->
    <section
        v-if="skills.length"
        class="border-t border-border px-6 pt-12 pb-24 sm:px-8 sm:pt-16 sm:pb-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] space-y-14 sm:space-y-16">
            <div
                v-for="(skillsInGroup, category, idx) in grouped"
                :key="category"
            >
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div
                        class="flex items-baseline gap-3 font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(idx + 1) }}</span>
                        <span class="text-foreground">{{ category }}</span>
                    </div>
                    <span
                        class="font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        {{ pad(skillsInGroup.length) }}
                    </span>
                </div>

                <div
                    class="grid grid-cols-1 gap-px border border-border bg-border sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="skill in skillsInGroup"
                        :key="skill.id"
                        class="group flex items-center gap-4 bg-background p-5 transition-colors hover:border-foreground hover:bg-muted/30"
                    >
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center border border-border bg-background transition-colors group-hover:border-foreground"
                        >
                            <img
                                v-if="isUrl(skill.icon)"
                                :src="skill.icon ?? ''"
                                :alt="skill.name"
                                class="h-6 w-6 object-contain"
                            />
                            <i
                                v-else-if="
                                    skill.icon && getDeviconClass(skill.icon)
                                "
                                :class="getDeviconClass(skill.icon)"
                                style="font-size: 1.5rem; line-height: 1"
                                aria-hidden="true"
                            />
                            <Code2
                                v-else
                                class="h-5 w-5 text-muted-foreground transition-colors group-hover:text-foreground"
                                :stroke-width="1.5"
                                aria-hidden="true"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-base font-medium tracking-[-0.01em] text-foreground sm:text-lg"
                            >
                                {{ skill.name }}
                            </p>
                            <span
                                v-if="skill.proficiency_label"
                                class="mt-1 inline-block font-mono text-[11px] tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ skill.proficiency_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ EMPTY STATE ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <p
                class="font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                {{ t('skills.empty') || 'Skills coming soon' }}
            </p>
        </div>
    </section>
</template>
