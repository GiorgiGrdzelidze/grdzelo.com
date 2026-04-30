<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowUpRight } from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';
import { useT } from '@/composables/useTranslate';

interface SocialLinkItem {
    platform: string;
    label: string | null;
    url: string;
    username: string | null;
    icon: string | null;
}

interface Props {
    settings: Record<string, any>;
    seo: Record<string, any>;
    budgetRanges: string[] | null;
}

defineProps<Props>();

const { t } = useT();
const page = usePage();

const socialLinks = computed(
    () => page.props.socialLinks as SocialLinkItem[] | undefined,
);

const form = useForm({
    name: '',
    email: '',
    company: '',
    subject: '',
    budget_range: '',
    project_type: '',
    message: '',
});

const clientErrors = ref<Record<string, string>>({});

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function validate(): boolean {
    const errs: Record<string, string> = {};

    if (!form.name.trim()) {
        errs.name =
            t('contact.errors.name_required') || 'Please tell me your name.';
    }

    if (!form.email.trim()) {
        errs.email =
            t('contact.errors.email_required') ||
            'I need an email to reply to.';
    } else if (!EMAIL_RE.test(form.email.trim())) {
        errs.email =
            t('contact.errors.email_invalid') ||
            'That email looks off — double-check it.';
    }

    if (!form.message.trim()) {
        errs.message =
            t('contact.errors.message_required') ||
            'Add a few words about what you need.';
    } else if (form.message.trim().length < 10) {
        errs.message =
            t('contact.errors.message_short') ||
            'A bit more detail helps me reply usefully.';
    }

    clientErrors.value = errs;

    if (Object.keys(errs).length) {
        nextTick(() => {
            const first = Object.keys(errs)[0];
            const el = document.getElementById(first);
            el?.focus();
            el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        return false;
    }

    return true;
}

function clearError(field: string): void {
    if (clientErrors.value[field]) {
        const next = { ...clientErrors.value };
        delete next[field];
        clientErrors.value = next;
    }

    if (form.errors[field as keyof typeof form.errors]) {
        form.clearErrors(field as keyof typeof form.errors);
    }
}

function errorFor(field: string): string | null {
    return (
        clientErrors.value[field] ??
        form.errors[field as keyof typeof form.errors] ??
        null
    );
}

function submit() {
    if (!validate()) {
        return;
    }

    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            clientErrors.value = {};
        },
    });
}

const fieldClass =
    'block w-full border border-border bg-background px-3 py-2.5 font-mono text-sm text-foreground placeholder:text-muted-foreground/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 focus-visible:ring-offset-background';
const fieldErrorClass = 'border-destructive focus-visible:ring-destructive';
</script>

<template>
    <!-- ============ HEADER ============ -->
    <section class="px-6 pt-24 pb-16 sm:px-8 sm:pt-32 sm:pb-20 lg:px-12">
        <div class="mx-auto max-w-[1200px]">
            <span class="eyebrow">{{ t('sections.contact.eyebrow') }}</span>
            <h1
                class="mt-6 max-w-[20ch] text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.04] font-semibold tracking-[-0.03em] text-balance"
            >
                {{ t('contact.title') }}<span class="text-accent">.</span>
            </h1>
            <p
                class="mt-8 max-w-[65ch] text-lg leading-relaxed text-pretty text-muted-foreground"
            >
                {{ t('contact.lead') }}
            </p>
        </div>
    </section>

    <!-- ============ BODY ============ -->
    <section
        class="border-t border-border px-6 pb-32 sm:px-8 sm:pb-40 lg:px-12"
    >
        <div class="mx-auto max-w-[1200px]">
            <div
                class="grid gap-px bg-border md:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]"
            >
                <!-- ====== Sidebar — facts ====== -->
                <aside class="flex flex-col gap-10 bg-background p-8 sm:p-10">
                    <div v-if="settings?.email">
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ t('contact.email_label') }}
                        </div>
                        <a
                            :href="`mailto:${settings.email}`"
                            class="mt-2 block font-mono text-sm text-foreground transition-colors hover:text-accent"
                        >
                            {{ settings.email }}
                        </a>
                    </div>

                    <div v-if="settings?.location">
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ t('contact.based_label') }}
                        </div>
                        <div class="mt-2 text-sm text-foreground">
                            {{ settings.location }}
                        </div>
                    </div>

                    <div>
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ t('contact.response_label') }}
                        </div>
                        <div class="mt-2 text-sm text-foreground">
                            {{ t('contact.response_value') }}
                        </div>
                    </div>

                    <div v-if="socialLinks?.length">
                        <div
                            class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                        >
                            {{ t('footer.elsewhere') }}
                        </div>
                        <ul class="mt-3 flex flex-col gap-2">
                            <li v-for="link in socialLinks" :key="link.url">
                                <a
                                    :href="link.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group inline-flex items-center gap-2 font-mono text-sm text-foreground transition-colors hover:text-accent"
                                    :aria-label="link.label || link.platform"
                                >
                                    {{ link.label || link.platform }}
                                    <ArrowUpRight
                                        class="h-3.5 w-3.5 text-muted-foreground transition-colors group-hover:text-accent"
                                    />
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>

                <!-- ====== Form ====== -->
                <div class="bg-background p-8 sm:p-10">
                    <form class="space-y-8" novalidate @submit.prevent="submit">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    for="name"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    {{ t('contact.name') }}
                                    <span class="text-accent">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    autocomplete="name"
                                    :aria-invalid="!!errorFor('name')"
                                    :aria-describedby="
                                        errorFor('name')
                                            ? 'name-error'
                                            : undefined
                                    "
                                    :class="[
                                        fieldClass,
                                        'mt-2',
                                        errorFor('name') ? fieldErrorClass : '',
                                    ]"
                                    @input="clearError('name')"
                                />
                                <Transition
                                    enter-active-class="transition duration-150"
                                    enter-from-class="opacity-0 -translate-y-0.5"
                                    enter-to-class="opacity-100 translate-y-0"
                                >
                                    <p
                                        v-if="errorFor('name')"
                                        id="name-error"
                                        class="mt-2 inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.04em] text-destructive"
                                        role="alert"
                                    >
                                        <AlertCircle
                                            class="h-3 w-3 shrink-0"
                                            :stroke-width="2"
                                            aria-hidden="true"
                                        />
                                        {{ errorFor('name') }}
                                    </p>
                                </Transition>
                            </div>
                            <div>
                                <label
                                    for="email"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    {{ t('contact.email') }}
                                    <span class="text-accent">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    :aria-invalid="!!errorFor('email')"
                                    :aria-describedby="
                                        errorFor('email')
                                            ? 'email-error'
                                            : undefined
                                    "
                                    :class="[
                                        fieldClass,
                                        'mt-2',
                                        errorFor('email')
                                            ? fieldErrorClass
                                            : '',
                                    ]"
                                    @input="clearError('email')"
                                />
                                <Transition
                                    enter-active-class="transition duration-150"
                                    enter-from-class="opacity-0 -translate-y-0.5"
                                    enter-to-class="opacity-100 translate-y-0"
                                >
                                    <p
                                        v-if="errorFor('email')"
                                        id="email-error"
                                        class="mt-2 inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.04em] text-destructive"
                                        role="alert"
                                    >
                                        <AlertCircle
                                            class="h-3 w-3 shrink-0"
                                            :stroke-width="2"
                                            aria-hidden="true"
                                        />
                                        {{ errorFor('email') }}
                                    </p>
                                </Transition>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    for="company"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    Company
                                </label>
                                <input
                                    id="company"
                                    v-model="form.company"
                                    type="text"
                                    autocomplete="organization"
                                    :class="[fieldClass, 'mt-2']"
                                />
                            </div>
                            <div>
                                <label
                                    for="subject"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    {{ t('contact.service') }}
                                </label>
                                <input
                                    id="subject"
                                    v-model="form.subject"
                                    type="text"
                                    :class="[fieldClass, 'mt-2']"
                                />
                            </div>
                        </div>

                        <div
                            v-if="budgetRanges?.length"
                            class="grid gap-6 sm:grid-cols-2"
                        >
                            <div>
                                <label
                                    for="project_type"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    Project type
                                </label>
                                <select
                                    id="project_type"
                                    v-model="form.project_type"
                                    :class="[fieldClass, 'mt-2']"
                                >
                                    <option value="">—</option>
                                    <option value="web_app">
                                        Web application
                                    </option>
                                    <option value="website">Website</option>
                                    <option value="consulting">
                                        Consulting
                                    </option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    for="budget_range"
                                    class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                                >
                                    {{ t('contact.budget') }}
                                </label>
                                <select
                                    id="budget_range"
                                    v-model="form.budget_range"
                                    :class="[fieldClass, 'mt-2']"
                                >
                                    <option value="">
                                        {{ t('contact.budget.unsure') }}
                                    </option>
                                    <option
                                        v-for="range in budgetRanges"
                                        :key="range"
                                        :value="range"
                                    >
                                        {{ range }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label
                                for="message"
                                class="font-mono text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase"
                            >
                                {{ t('contact.message') }}
                                <span class="text-accent">*</span>
                            </label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="6"
                                :placeholder="t('contact.message_placeholder')"
                                :aria-invalid="!!errorFor('message')"
                                :aria-describedby="
                                    errorFor('message')
                                        ? 'message-error'
                                        : undefined
                                "
                                :class="[
                                    fieldClass,
                                    'mt-2 resize-y',
                                    errorFor('message') ? fieldErrorClass : '',
                                ]"
                                @input="clearError('message')"
                            />
                            <Transition
                                enter-active-class="transition duration-150"
                                enter-from-class="opacity-0 -translate-y-0.5"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <p
                                    v-if="errorFor('message')"
                                    id="message-error"
                                    class="mt-2 inline-flex items-center gap-1.5 font-mono text-[11px] tracking-[0.04em] text-destructive"
                                    role="alert"
                                >
                                    <AlertCircle
                                        class="h-3 w-3 shrink-0"
                                        :stroke-width="2"
                                        aria-hidden="true"
                                    />
                                    {{ errorFor('message') }}
                                </p>
                            </Transition>
                        </div>

                        <div
                            class="flex flex-col gap-4 border-t border-border pt-8 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <p
                                class="max-w-[40ch] text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ t('contact.privacy') }}
                            </p>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="group inline-flex items-center justify-center gap-2 bg-foreground px-6 py-3 text-sm font-medium text-background transition-opacity hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? t('contact.sending')
                                        : t('contact.send')
                                }}
                                <ArrowUpRight
                                    class="h-4 w-4 text-accent transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                                />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
