import { usePage } from '@inertiajs/vue3';

/**
 * useLocalePath — prefix the active locale onto a public-site path.
 *
 * Public URLs are /{locale}/... by design (PR #11). Inertia <Link> hrefs
 * built from inline templates would otherwise emit unprefixed paths and
 * rely on RedirectUnprefixedPathsController to add the locale from the
 * cookie — which can land on the wrong locale if the cookie is stale.
 *
 * Usage:
 *   const localePath = useLocalePath();
 *   <Link :href="localePath(`/projects/${project.slug}`)" />
 */
export function useLocalePath() {
    const page = usePage();

    return (path: string): string => {
        const locale = (page.props.locale as string) || 'en';
        const normalized = path.startsWith('/') ? path : `/${path}`;

        return `/${locale}${normalized}`;
    };
}
