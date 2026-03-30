<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Github, Linkedin, Twitter } from 'lucide-vue-next';

interface Props {
    settings?: Record<string, any>;
}

defineProps<Props>();

const footerNav = [
    {
        title: 'Explore',
        links: [
            { label: 'Projects', href: '/projects' },
            { label: 'Blog', href: '/blog' },
            { label: 'Services', href: '/services' },
            { label: 'About', href: '/about' },
        ],
    },
    {
        title: 'More',
        links: [
            { label: 'Skills', href: '/skills' },
            { label: 'Experience', href: '/experience' },
            { label: 'Hobbies', href: '/hobbies' },
            { label: 'Contact', href: '/contact' },
        ],
    },
];
</script>

<template>
    <footer class="border-t border-border/40 bg-background">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <!-- Brand Column -->
                <div class="md:col-span-2">
                    <Link href="/" class="text-lg font-semibold tracking-tight">
                        {{ settings?.brand_name || 'grdzelo' }}
                    </Link>
                    <p class="mt-3 max-w-md text-sm text-muted-foreground">
                        {{ settings?.tagline || 'Product-Minded Software Engineer' }}
                    </p>
                    <p v-if="settings?.footer_text" class="mt-2 text-sm text-muted-foreground">
                        {{ settings.footer_text }}
                    </p>
                </div>

                <!-- Nav Columns -->
                <div v-for="group in footerNav" :key="group.title">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-foreground">
                        {{ group.title }}
                    </h3>
                    <ul class="mt-3 space-y-2">
                        <li v-for="link in group.links" :key="link.href">
                            <Link
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-border/40 pt-6 sm:flex-row">
                <p class="text-xs text-muted-foreground">
                    {{ settings?.copyright_text || `© ${new Date().getFullYear()} grdzelo.com. All rights reserved.` }}
                </p>

                <div class="flex items-center gap-3">
                    <a
                        href="https://github.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                        aria-label="GitHub"
                    >
                        <Github class="h-4 w-4" />
                    </a>
                    <a
                        href="https://linkedin.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                        aria-label="LinkedIn"
                    >
                        <Linkedin class="h-4 w-4" />
                    </a>
                    <a
                        href="https://twitter.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                        aria-label="Twitter"
                    >
                        <Twitter class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
