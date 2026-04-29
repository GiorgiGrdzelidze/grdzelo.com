const statusStyles: Record<string, string> = {
    active: 'bg-emerald-500/10 text-emerald-800 ring-emerald-500/20 dark:text-emerald-300 dark:ring-emerald-400/30',
    experimental:
        'bg-amber-500/10 text-amber-800 ring-amber-500/20 dark:text-amber-300 dark:ring-amber-400/30',
    archived:
        'bg-zinc-500/10 text-zinc-800 ring-zinc-500/20 dark:text-zinc-300 dark:ring-zinc-400/30',
    deprecated:
        'bg-rose-500/10 text-rose-800 ring-rose-500/20 dark:text-rose-300 dark:ring-rose-400/30',
};

const fallback =
    'bg-zinc-500/10 text-zinc-800 ring-zinc-500/20 dark:text-zinc-300 dark:ring-zinc-400/30';

export function repositoryStatusClass(status: string): string {
    return statusStyles[status] ?? fallback;
}
