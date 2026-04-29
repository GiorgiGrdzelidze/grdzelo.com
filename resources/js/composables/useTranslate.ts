import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * useTranslate — minimal i18n composable for Inertia + Laravel.
 *
 * Translations are shared as a flat key/value map via the
 * HandleInertiaRequests middleware (page.props.translations).
 *
 * Usage:
 *   const { t, locale } = useT();
 *   t('hero.title_1')
 *   t('article.read_time', { minutes: 5 })   // {minutes} placeholder
 */
export function useT() {
    const page = usePage();

    const locale = computed<string>(
        () => (page.props.locale as string) || 'en',
    );

    const translations = computed<Record<string, string>>(
        () => (page.props.translations as Record<string, string>) || {},
    );

    function t(
        key: string,
        replacements?: Record<string, string | number>,
    ): string {
        let str = translations.value[key];

        if (str === undefined) {
            // Dev-friendly fallback: render the last segment as a humanized string
            if (import.meta.env.DEV) {
                console.warn(
                    `[i18n] Missing key: ${key} (locale: ${locale.value})`,
                );
            }

            str = key.split('.').pop() ?? key;
        }

        if (replacements) {
            for (const [k, v] of Object.entries(replacements)) {
                str = str.replace(new RegExp(`\\{${k}\\}`, 'g'), String(v));
            }
        }

        return str;
    }

    return { t, locale, translations };
}
