import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import PublicLayout from '@/layouts/PublicLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'grdzelo.com';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name.startsWith('Public/'):
                return PublicLayout;
            case name === 'Welcome':
                return null;
            default:
                return PublicLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
