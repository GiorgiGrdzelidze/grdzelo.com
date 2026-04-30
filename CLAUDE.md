# CLAUDE.md

Guidance for Claude Code (and other AI assistants) when working in this repository.

## Project at a glance

`grdzelo.com` is a single Laravel application that serves two surfaces:

1. **Public personal-brand site** — marketing pages for Giorgi Grdzelidze (Home, Projects, Blog, Services, Repositories, Gallery, About, Skills, Experience, Hobbies, Social, Education, Certifications, Contact). SSR-enabled, SEO-tuned.
2. **Private admin + finance CMS** — Filament v5 panel at `/admin`, manages all public content plus a multi-currency finance module (income, expenses, salaries, exchange rates, subscriptions with reminders).

Auth is consolidated into Filament. There are intentionally **no public auth routes** (no `/login`, no `/register`) — do not reintroduce them.

## Stack

| Layer    | Technology |
|----------|------------|
| Backend  | Laravel 13.2 · PHP 8.3+ (CI runs on 8.4) · MySQL 8.4 (Sail) / SQLite in `.env.example` |
| Admin    | Filament v5 · Spatie (settings, activitylog, medialibrary, sitemap, schema-org, tags) · Fortify (2FA) |
| Frontend | Vue 3.5 + TypeScript · Inertia.js v3 · Tailwind CSS v4 · shadcn-vue (`new-york-v4`, neutral base) · lucide-vue-next · reka-ui · `@vueuse/core` |
| Build    | Vite 8 · `laravel-vite-plugin` · `@laravel/vite-plugin-wayfinder` · SSR entry `resources/js/ssr.ts` |
| Tests    | Pest 4 (+ pest-plugin-laravel) |
| Lint     | Laravel Pint (PHP) · ESLint + Prettier + `prettier-plugin-tailwindcss` (JS/TS/Vue) · `vue-tsc` for types |

## Layout

```
app/
  Concerns/                 # Reusable traits
  Console/                  # Artisan + scheduler
  Enums/                    # PHP backed enums (Currency, BillingInterval, SubscriptionStatus, ...)
  Filament/                 # Admin panel: Pages, Resources, Widgets
  Http/Controllers/Public/  # Inertia controllers for public pages
  Models/                   # Eloquent models (public content + finance domain)
  Notifications/
  Providers/
  Settings/                 # Spatie settings classes
config/                     # Standard Laravel config
database/                   # migrations / factories / seeders
resources/
  css/app.css               # Tailwind v4 entrypoint
  js/
    app.ts                  # Inertia client entry
    ssr.ts                  # SSR entry
    layouts/PublicLayout.vue
    pages/Public/           # Vue pages — names match Inertia::render('Public/Xxx')
    components/ui/          # shadcn-vue components
    components/public/      # Site-specific components (SeoHead, SiteHeader, SiteFooter, ...)
routes/
  web.php                   # Public routes only — admin is mounted by Filament
  console.php
storage/app/public/         # User uploads (media library)
tests/                      # Pest suites
```

## Commands

```bash
# One-shot dev (server + queue + pail logs + vite, via concurrently):
composer dev

# Individual:
php artisan serve
npm run dev                 # Vite (HMR)
npm run build               # production assets
npm run build:ssr           # SSR + client builds

# Quality gates (mirrors CI):
composer lint               # Pint
composer lint:check         # Pint --test
npm run lint:check          # ESLint, no autofix
npm run format:check        # Prettier check
npm run types:check         # vue-tsc --noEmit
composer ci:check           # full CI mirror locally
composer test               # config:clear → lint:check → artisan test
./vendor/bin/pest           # raw Pest

# First-time setup:
composer setup              # composer install → .env → key → migrate → npm install → npm run build
```

CI runs `lint.yml` and `tests.yml` on push/PR to `develop`, `main`, `master`, `workos`. Build step in tests workflow runs `npm run build` before Pest, so view files compile.

## Conventions

- **PHP**: strict types where possible; backed enums for finance domain; Pint formatting (config: `pint.json`).
- **Eloquent**: query scopes are used heavily on public-facing models — `published()`, `visible()`, `featured()`, `ordered()`. Reuse them rather than rewriting filters in controllers.
- **Inertia**: every public controller extends `BasePublicController` and merges `$this->sharedProps()` + `$this->seoFor(...)` into the response. Follow the pattern.
- **Vue**: `<script setup lang="ts">`, `defineProps<Props>()` with explicit interfaces, Tailwind utility classes only (Tailwind v4, no separate `tailwind.config.js` runtime — see `vite.config.ts`).
- **shadcn-vue**: components live in `resources/js/components/ui/` per `components.json` aliases (`@/components`, `@/components/ui`, `@/composables`, `@/lib/utils`).
- **SEO**: meta tags must render server-side via the Blade root template (see commit `d99eab6`). Don't move them client-only.
- **Finance data**: amounts always carry a currency snapshot + an exchange-rate-resolved base amount. Preserve both when refactoring; reporting widgets depend on the base-currency field.
- **Subscriptions**: lifecycle changes go through `SubscriptionEvent` records — never mutate `Subscription.status` without writing the event.

## Working with the Filament admin

Filament v5 lives under `app/Filament/{Pages,Resources,Widgets}`. Admin URL is `/admin`. Authentication is via Fortify (2FA-capable). The `User` model has a `canAccessPanel()` method (commit `94b73c9`) — keep it gating admin access.

## Routing

Public routes are in `routes/web.php`. Notable entries:

- `/` — `HomeController::__invoke`
- `/projects`, `/projects/{project}`, `/blog`, `/blog/{article}`, `/services`, `/repositories`, `/gallery`, `/gallery/{album}`, `/about`, `/skills`, `/experience`, `/hobbies`, `/hobbies/{hobby}`, `/social`, `/education`, `/certifications`, `/contact` (GET + POST)
- `/sitemap.xml`, `/robots.txt` — `SitemapController`

When adding a public page, mirror an existing controller — they all return `Inertia::render('Public/<Name>', [...])` and the Vue file lives at `resources/js/pages/Public/<Name>.vue`.

## Things to avoid

- Adding public auth/registration routes — auth is Filament-only by design.
- Removing the SSR build step or moving SEO meta tags client-only — bots need them rendered.
- Mocking the database in tests if it's covering finance migrations or subscription state transitions — prefer real DB tests (Pest + sqlite in-memory is the CI default).
- Bumping PHP language features past what `php-version: 8.4` in CI accepts.
- Storing real finance values (amounts, balances) in commits, screenshots, or test fixtures.

## Subagents

Specialized agents live in `.claude/agents/`. They cover site analysis, content writing, code review, and SEO/performance auditing. Invoke them when their domain matches the task.
