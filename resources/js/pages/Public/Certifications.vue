<script setup lang="ts">
import { Award, Calendar, ExternalLink } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface SkillItem {
    id: number;
    name: string;
    slug: string;
}

interface CertificationItem {
    id: number;
    title: string;
    issuing_organization: string;
    issue_date: string;
    expiry_date: string | null;
    no_expiry: boolean;
    credential_id: string | null;
    credential_url: string | null;
    description: string | null;
    badge_image: string | null;
    is_featured: boolean;
    skills: SkillItem[];
}

interface Props {
    certifications: CertificationItem[];
}

defineProps<Props>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
    });
}

function isExpired(cert: CertificationItem): boolean {
    if (cert.no_expiry) {
        return false;
    }

    if (!cert.expiry_date) {
        return false;
    }

    return new Date(cert.expiry_date) < new Date();
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">
                    Certifications
                </h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Professional certifications and credentials that validate my
                    skills.
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2">
                <div
                    v-for="cert in certifications"
                    :key="cert.id"
                    class="group relative rounded-xl border border-border/40 bg-card p-6 transition-all hover:border-border hover:shadow-sm"
                >
                    <div class="flex items-start gap-4">
                        <div v-if="cert.badge_image" class="shrink-0">
                            <img
                                :src="`/storage/${cert.badge_image}`"
                                :alt="cert.title"
                                class="h-14 w-14 rounded-lg object-contain"
                            />
                        </div>
                        <div
                            v-else
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <Award class="h-6 w-6 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h2
                                    class="text-base leading-tight font-semibold"
                                >
                                    {{ cert.title }}
                                </h2>
                                <Badge
                                    v-if="cert.is_featured"
                                    variant="secondary"
                                    class="shrink-0 text-xs"
                                    >Featured</Badge
                                >
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ cert.issuing_organization }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 flex items-center gap-3 text-xs text-muted-foreground"
                    >
                        <span class="flex items-center gap-1">
                            <Calendar class="h-3 w-3" />
                            Issued {{ formatDate(cert.issue_date) }}
                        </span>
                        <Badge
                            v-if="isExpired(cert)"
                            variant="destructive"
                            class="text-xs"
                            >Expired</Badge
                        >
                        <Badge
                            v-else-if="cert.no_expiry"
                            variant="outline"
                            class="text-xs"
                            >No Expiry</Badge
                        >
                        <span v-else-if="cert.expiry_date" class="text-xs">
                            Expires {{ formatDate(cert.expiry_date) }}
                        </span>
                    </div>

                    <p
                        v-if="cert.description"
                        class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ cert.description }}
                    </p>

                    <div
                        v-if="cert.skills?.length"
                        class="mt-3 flex flex-wrap gap-1"
                    >
                        <Badge
                            v-for="skill in cert.skills"
                            :key="skill.id"
                            variant="secondary"
                            class="text-xs"
                            >{{ skill.name }}</Badge
                        >
                    </div>

                    <div
                        v-if="cert.credential_id || cert.credential_url"
                        class="mt-4 flex items-center gap-3 border-t border-border/30 pt-3 text-xs text-muted-foreground"
                    >
                        <span v-if="cert.credential_id"
                            >ID: {{ cert.credential_id }}</span
                        >
                        <a
                            v-if="cert.credential_url"
                            :href="cert.credential_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ml-auto flex items-center gap-1 text-primary hover:underline"
                        >
                            Verify
                            <ExternalLink class="h-3 w-3" />
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="!certifications.length" class="mt-14 text-center">
                <p class="text-lg font-medium">Certifications coming soon</p>
            </div>
        </div>
    </section>
</template>
