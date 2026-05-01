<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Code2 } from 'lucide-vue-next';
import { useT } from '@/composables/useTranslate';

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
    settings: Record<string, any>;
    seo: Record<string, any>;
    services: ServiceItem[];
}

defineProps<Props>();

const { t } = useT();

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

const processSteps = [
    {
        key: 'intro',
        title: 'services.process.intro.title',
        body: 'services.process.intro.body',
    },
    {
        key: 'scope',
        title: 'services.process.scope.title',
        body: 'services.process.scope.body',
    },
    {
        key: 'build',
        title: 'services.process.build.title',
        body: 'services.process.build.body',
    },
    {
        key: 'ship',
        title: 'services.process.ship.title',
        body: 'services.process.ship.body',
    },
];
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.services.eyebrow') }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('services.title') }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('services.lead') }}
            </p>
        </div>
    </section>

    <!-- ============ SERVICES GRID ============ -->
    <section
        v-if="services.length"
        class="border-t border-border px-6 pb-32 sm:px-8 sm:pb-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div
                class="grid grid-cols-1 gap-px border-b border-border bg-border md:grid-cols-2 lg:grid-cols-3"
            >
                <article
                    v-for="(service, i) in services"
                    :key="service.id"
                    class="group flex flex-col bg-background p-8 transition-colors hover:bg-muted/30"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        <span>/ {{ pad(i + 1) }}</span>
                    </div>

                    <h2
                        class="mt-6 text-xl font-semibold tracking-[-0.01em] text-foreground"
                    >
                        {{ service.title }}
                    </h2>

                    <p
                        class="mt-4 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ service.summary }}
                    </p>

                    <div
                        v-if="service.description"
                        class="prose prose-sm mt-6 max-w-none text-sm leading-relaxed text-foreground prose-neutral dark:prose-invert"
                        v-html="service.description"
                    />

                    <div class="mt-auto pt-8">
                        <Link
                            v-if="service.cta_url"
                            :href="service.cta_url"
                            class="group/cta inline-flex items-center gap-2 font-mono text-[11px] font-medium tracking-[0.12em] text-foreground uppercase transition-colors hover:text-accent"
                        >
                            {{ service.cta_text || t('services.discuss_cta') }}
                            <ArrowUpRight
                                class="h-3.5 w-3.5 text-accent transition-transform group-hover/cta:translate-x-0.5 group-hover/cta:-translate-y-0.5"
                                aria-hidden="true"
                            />
                        </Link>
                        <Link
                            v-else
                            href="/contact"
                            class="group/cta inline-flex items-center gap-2 font-mono text-[11px] font-medium tracking-[0.12em] text-foreground uppercase transition-colors hover:text-accent"
                        >
                            {{ t('services.discuss_cta') }}
                            <ArrowUpRight
                                class="h-3.5 w-3.5 text-accent transition-transform group-hover/cta:translate-x-0.5 group-hover/cta:-translate-y-0.5"
                                aria-hidden="true"
                            />
                        </Link>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============ EMPTY STATE ============ -->
    <section
        v-else
        class="border-t border-border px-6 py-32 sm:px-8 sm:py-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px] text-center">
            <Code2
                class="mx-auto h-10 w-10 text-muted-foreground"
                :stroke-width="1.5"
                aria-hidden="true"
            />
            <p
                class="mt-6 font-mono text-xs tracking-[0.12em] text-muted-foreground uppercase"
            >
                Services coming soon
            </p>
        </div>
    </section>

    <!-- ============ PROCESS ============ -->
    <section
        class="border-t border-border bg-muted/40 px-6 py-24 sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="mb-16 max-w-[55ch]">
                <span class="eyebrow">{{ t('services.process_eyebrow') }}</span>
                <h2
                    class="mt-4 text-[clamp(1.875rem,3vw,2.25rem)] font-semibold tracking-[-0.02em] text-balance"
                >
                    {{ t('services.process_title') }}
                </h2>
            </div>

            <ol
                class="grid grid-cols-1 gap-px border border-border bg-border sm:grid-cols-2 lg:grid-cols-4"
            >
                <li
                    v-for="(step, i) in processSteps"
                    :key="step.key"
                    class="bg-background p-8"
                >
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                    >
                        / {{ pad(i + 1) }}
                    </div>
                    <h3
                        class="mt-6 text-base font-semibold tracking-[-0.01em] text-foreground"
                    >
                        {{ t(step.title) }}
                    </h3>
                    <p
                        class="mt-3 text-sm leading-relaxed text-pretty text-muted-foreground"
                    >
                        {{ t(step.body) }}
                    </p>
                </li>
            </ol>
        </div>
    </section>

    <!-- ============ CONTACT CTA ============ -->
    <section
        class="border-t border-border bg-foreground px-6 py-24 text-background sm:px-8 sm:py-32 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div
                class="grid gap-12 lg:grid-cols-[1.2fr_minmax(0,1fr)] lg:items-end"
            >
                <div>
                    <span
                        class="font-mono text-[11px] font-medium tracking-[0.12em] text-background/60 uppercase"
                    >
                        {{ t('sections.contact.eyebrow') }}
                    </span>
                    <h2
                        class="mt-4 max-w-[20ch] text-[clamp(2rem,4vw,3rem)] leading-[1.05] font-semibold tracking-[-0.02em] text-balance"
                    >
                        {{ t('contact.title')
                        }}<span class="text-accent">.</span>
                    </h2>
                </div>
                <div class="flex justify-start lg:justify-end">
                    <Link
                        href="/contact"
                        class="group inline-flex items-center gap-2 bg-background px-6 py-3 text-sm font-medium text-foreground transition-opacity hover:opacity-90 active:scale-[0.98]"
                    >
                        {{ t('cta.get_in_touch') }}
                        <ArrowUpRight
                            class="h-4 w-4 text-accent transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            aria-hidden="true"
                        />
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
