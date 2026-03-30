<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle, Mail, MapPin, Phone } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface SocialLinkItem {
    platform: string;
    label: string | null;
    url: string;
    username: string | null;
    icon: string | null;
}

interface Props {
    settings: Record<string, any>;
    budgetRanges: string[] | null;
}

defineProps<Props>();
const page = usePage();

const flash = computed(() => (page.props.flash as Record<string, string> | undefined));
const socialLinks = computed(() => page.props.socialLinks as SocialLinkItem[] | undefined);

const form = useForm({
    name: '',
    email: '',
    company: '',
    subject: '',
    budget_range: '',
    project_type: '',
    message: '',
});

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight">Get in Touch</h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Have a project in mind? I'd love to hear from you. Fill out the form below and I'll get back to you as soon as possible.
                </p>
            </div>

            <div class="mt-14 grid gap-12 lg:grid-cols-3">
                <!-- Contact Info -->
                <div class="space-y-6">
                    <Card v-if="settings?.email">
                        <CardContent class="flex items-center gap-4 py-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Email</p>
                                <a :href="`mailto:${settings.email}`" class="text-sm text-muted-foreground hover:text-foreground">
                                    {{ settings.email }}
                                </a>
                            </div>
                        </CardContent>
                    </Card>
                    <Card v-if="settings?.phone">
                        <CardContent class="flex items-center gap-4 py-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Phone</p>
                                <p class="text-sm text-muted-foreground">{{ settings.phone }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card v-if="settings?.location">
                        <CardContent class="flex items-center gap-4 py-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">Location</p>
                                <p class="text-sm text-muted-foreground">{{ settings.location }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Social Links -->
                    <div v-if="socialLinks?.length" class="pt-4">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-foreground">Connect</h3>
                        <div class="flex flex-wrap gap-3">
                            <a
                                v-for="link in socialLinks"
                                :key="link.url"
                                :href="link.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-2 rounded-lg border border-border/40 bg-background px-4 py-2.5 text-sm transition-colors hover:bg-accent"
                                :aria-label="link.label || link.platform"
                            >
                                <i v-if="link.icon" :class="link.icon" class="text-lg" />
                                <span>{{ link.label || link.platform }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <!-- Success Message -->
                    <div v-if="flash?.success" class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300">
                        <CheckCircle class="h-5 w-5 shrink-0" />
                        <p class="text-sm">{{ flash.success }}</p>
                    </div>

                    <Card>
                        <CardHeader>
                            <CardTitle>Send a Message</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form class="space-y-6" @submit.prevent="submit">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="name">Name *</Label>
                                        <Input id="name" v-model="form.name" :class="{ 'border-destructive': form.errors.name }" placeholder="Your name" />
                                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="email">Email *</Label>
                                        <Input id="email" v-model="form.email" type="email" :class="{ 'border-destructive': form.errors.email }" placeholder="your@email.com" />
                                        <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="company">Company</Label>
                                        <Input id="company" v-model="form.company" placeholder="Your company" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="subject">Subject</Label>
                                        <Input id="subject" v-model="form.subject" placeholder="What is this about?" />
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="project_type">Project Type</Label>
                                        <Select v-model="form.project_type">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="web_app">Web Application</SelectItem>
                                                <SelectItem value="website">Website</SelectItem>
                                                <SelectItem value="consulting">Consulting</SelectItem>
                                                <SelectItem value="other">Other</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div v-if="budgetRanges?.length" class="space-y-2">
                                        <Label for="budget_range">Budget Range</Label>
                                        <Select v-model="form.budget_range">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select range" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="range in budgetRanges" :key="range" :value="range">
                                                    {{ range }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="message">Message *</Label>
                                    <textarea
                                        id="message"
                                        v-model="form.message"
                                        rows="5"
                                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        :class="{ 'border-destructive': form.errors.message }"
                                        placeholder="Tell me about your project..."
                                    />
                                    <p v-if="form.errors.message" class="text-xs text-destructive">{{ form.errors.message }}</p>
                                </div>

                                <Button type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>
