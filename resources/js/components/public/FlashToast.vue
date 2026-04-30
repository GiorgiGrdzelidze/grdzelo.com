<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { AlertCircle, Check, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useT } from '@/composables/useTranslate';

const { t } = useT();

interface FlashShape {
    success?: string | null;
    error?: string | null;
}

const page = usePage();

const flash = computed(
    () => (page.props.flash as FlashShape | undefined) ?? {},
);

const visible = ref(false);
const variant = ref<'success' | 'error'>('success');
const message = ref<string>('');

let dismissTimer: ReturnType<typeof setTimeout> | null = null;

function show(kind: 'success' | 'error', text: string): void {
    variant.value = kind;
    message.value = text;
    visible.value = true;

    if (dismissTimer) {
        clearTimeout(dismissTimer);
    }

    dismissTimer = setTimeout(() => {
        visible.value = false;
    }, 5000);
}

function dismiss(): void {
    visible.value = false;

    if (dismissTimer) {
        clearTimeout(dismissTimer);
        dismissTimer = null;
    }
}

watch(
    () => [flash.value.success, flash.value.error] as const,
    ([success, error]) => {
        if (success) {
            show('success', success);
        } else if (error) {
            show('error', error);
        }
    },
    { immediate: true },
);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="visible"
                class="fixed right-4 bottom-4 z-50 flex max-w-[420px] items-start gap-3 border bg-background p-4 shadow-sm sm:right-6 sm:bottom-6"
                :class="
                    variant === 'success'
                        ? 'border-accent'
                        : 'border-destructive'
                "
                role="status"
                aria-live="polite"
            >
                <Check
                    v-if="variant === 'success'"
                    class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                    :stroke-width="2"
                    aria-hidden="true"
                />
                <AlertCircle
                    v-else
                    class="mt-0.5 h-4 w-4 shrink-0 text-destructive"
                    :stroke-width="2"
                    aria-hidden="true"
                />

                <div class="min-w-0 flex-1">
                    <div
                        class="font-mono text-[11px] font-medium tracking-[0.12em] uppercase"
                        :class="
                            variant === 'success'
                                ? 'text-accent'
                                : 'text-destructive'
                        "
                    >
                        {{
                            variant === 'success'
                                ? t('flash.success_label') || 'Sent'
                                : t('flash.error_label') || 'Error'
                        }}
                    </div>
                    <p class="mt-1.5 text-sm leading-relaxed text-foreground">
                        {{ message }}
                    </p>
                </div>

                <button
                    type="button"
                    class="shrink-0 text-muted-foreground transition-colors hover:text-foreground"
                    :aria-label="t('flash.dismiss') || 'Dismiss'"
                    @click="dismiss"
                >
                    <X class="h-4 w-4" :stroke-width="2" aria-hidden="true" />
                </button>
            </div>
        </Transition>
    </Teleport>
</template>
