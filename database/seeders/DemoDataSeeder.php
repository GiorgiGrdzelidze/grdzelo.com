<?php

namespace Database\Seeders;

use App\Enums\BillingInterval;
use App\Enums\Currency;
use App\Enums\IncomeType;
use App\Enums\PayFrequency;
use App\Enums\SubscriptionEventType;
use App\Enums\SubscriptionStatus;
use App\Models\Album;
use App\Models\ContactSubmission;
use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\IncomeEntry;
use App\Models\IncomeSource;
use App\Models\Repository;
use App\Models\SalaryRecord;
use App\Models\SocialFeedItem;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\SubscriptionReminder;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRepositories();
        $this->seedAlbums();
        $this->seedSocialFeedItems();
        $this->seedContactSubmissions();
        $this->seedExchangeRates();
        $this->seedExpenseCategories();
        $this->seedExpenses();
        $this->seedIncomeSources();
        $this->seedSalaryRecords();
        $this->seedSubscriptions();
    }

    private function seedRepositories(): void
    {
        $items = [
            [
                'name' => 'fina-sdk-laravel',
                'slug' => 'fina-sdk-laravel',
                'url' => 'https://github.com/grdzelo/fina-sdk-laravel',
                'summary' => 'Laravel SDK for the Georgian National Bank FINA identity-verification platform. mTLS, signing, retries, typed exceptions.',
                'owner' => 'grdzelo',
                'language' => 'PHP',
                'technologies' => ['Laravel', 'Guzzle', 'mTLS', 'PHPUnit'],
                'stars' => 142,
                'forks' => 18,
                'status' => 'active',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 1,
                'demo_url' => null,
            ],
            [
                'name' => 'inertia-seo-toolkit',
                'slug' => 'inertia-seo-toolkit',
                'url' => 'https://github.com/grdzelo/inertia-seo-toolkit',
                'summary' => 'SSR-aware meta + JSON-LD helpers for Laravel + Inertia + Vue 3. Plays nicely with Spatie sitemap.',
                'owner' => 'grdzelo',
                'language' => 'PHP',
                'technologies' => ['Laravel', 'Inertia.js', 'Vue 3', 'TypeScript'],
                'stars' => 87,
                'forks' => 9,
                'status' => 'active',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 2,
                'demo_url' => 'https://grdzelo.com',
            ],
            [
                'name' => 'filament-finance-kit',
                'slug' => 'filament-finance-kit',
                'url' => 'https://github.com/grdzelo/filament-finance-kit',
                'summary' => 'Multi-currency income, expense, subscription tracking for Filament v5. Used in production on this very site.',
                'owner' => 'grdzelo',
                'language' => 'PHP',
                'technologies' => ['Filament', 'Laravel', 'Spatie Settings'],
                'stars' => 64,
                'forks' => 7,
                'status' => 'active',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 3,
                'demo_url' => null,
            ],
            [
                'name' => 'tailwind-editorial-kit',
                'slug' => 'tailwind-editorial-kit',
                'url' => 'https://github.com/grdzelo/tailwind-editorial-kit',
                'summary' => 'Hairline-rule, mono-eyebrow, accent-period building blocks for editorial Tailwind layouts.',
                'owner' => 'grdzelo',
                'language' => 'TypeScript',
                'technologies' => ['Tailwind CSS v4', 'Vue 3', 'shadcn-vue'],
                'stars' => 38,
                'forks' => 4,
                'status' => 'active',
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 4,
                'demo_url' => null,
            ],
            [
                'name' => 'octane-deploy-recipe',
                'slug' => 'octane-deploy-recipe',
                'url' => 'https://github.com/grdzelo/octane-deploy-recipe',
                'summary' => 'Zero-downtime Octane + Swoole deploy recipe with healthcheck, graceful reload, and queue worker rotation.',
                'owner' => 'grdzelo',
                'language' => 'Shell',
                'technologies' => ['Laravel Octane', 'Swoole', 'systemd', 'nginx'],
                'stars' => 21,
                'forks' => 3,
                'status' => 'maintenance',
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 5,
                'demo_url' => null,
            ],
            [
                'name' => 'gel-currency-php',
                'slug' => 'gel-currency-php',
                'url' => 'https://github.com/grdzelo/gel-currency-php',
                'summary' => 'Lightweight NBG exchange-rate client + currency conversion helpers. Caches rates and falls back gracefully.',
                'owner' => 'grdzelo',
                'language' => 'PHP',
                'technologies' => ['PHP 8.3+', 'PSR-18', 'PSR-6 cache'],
                'stars' => 14,
                'forks' => 2,
                'status' => 'archived',
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 6,
                'demo_url' => null,
            ],
        ];

        foreach ($items as $item) {
            Repository::create($item);
        }
    }

    private function seedAlbums(): void
    {
        $albums = [
            [
                'title' => 'Kazbegi — autumn ridge',
                'slug' => 'kazbegi-autumn-ridge',
                'summary' => 'Late-October trek up to Gergeti Trinity and the Sabertse pass. Cold mornings, warm light, no other hikers.',
                'description' => '<p>A four-day trek across the Kazbegi region in late October. The trees had turned, the cafés in Stepantsminda were quiet, and the first snow had just touched the upper passes.</p>',
                'status' => 'published',
                'publish_at' => now()->subDays(28),
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 1,
                'location' => 'Kazbegi, Georgia',
                'taken_at' => '2025-10-19',
            ],
            [
                'title' => 'Tusheti — Diklo to Omalo',
                'slug' => 'tusheti-diklo-to-omalo',
                'summary' => 'Three-day traverse through the medieval defensive towers of Diklo and back to Omalo. Pure remote.',
                'description' => '<p>Tusheti is the kind of place that resets your sense of what "off-grid" means. We carried everything in, including the cellular dead zones.</p>',
                'status' => 'published',
                'publish_at' => now()->subDays(95),
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 2,
                'location' => 'Tusheti, Georgia',
                'taken_at' => '2025-08-12',
            ],
            [
                'title' => 'Tbilisi — old town frames',
                'slug' => 'tbilisi-old-town-frames',
                'summary' => 'A weekend walking the back alleys of Sololaki and Avlabari with a 35mm prime.',
                'description' => '<p>Street photography is mostly waiting. These came out of two long afternoons.</p>',
                'status' => 'published',
                'publish_at' => now()->subDays(140),
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 3,
                'location' => 'Tbilisi, Georgia',
                'taken_at' => '2025-06-28',
            ],
            [
                'title' => 'Svaneti — towers in fog',
                'slug' => 'svaneti-towers-in-fog',
                'summary' => 'Mestia, Ushguli, the silence between them. Drafted, not yet published.',
                'description' => '<p>Working draft.</p>',
                'status' => 'draft',
                'publish_at' => null,
                'is_featured' => false,
                'is_visible' => false,
                'sort_order' => 4,
                'location' => 'Svaneti, Georgia',
                'taken_at' => '2025-09-04',
            ],
        ];

        foreach ($albums as $album) {
            Album::create($album);
        }
    }

    private function seedSocialFeedItems(): void
    {
        $items = [
            [
                'platform' => 'instagram',
                'media_url' => 'https://instagram.com/p/demo-1',
                'thumbnail_url' => null,
                'caption' => 'First snow on Kazbek. Worth the 4am start.',
                'external_url' => 'https://instagram.com/p/demo-1',
                'posted_at' => now()->subDays(3),
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'platform' => 'twitter',
                'media_url' => null,
                'thumbnail_url' => null,
                'caption' => 'Released v2.3 of fina-sdk-laravel — adds idempotency keys + retry budgets. Composer require it like a normal package now.',
                'external_url' => 'https://x.com/grdzelo/status/demo',
                'posted_at' => now()->subDays(8),
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 2,
            ],
            [
                'platform' => 'github',
                'media_url' => null,
                'thumbnail_url' => null,
                'caption' => 'Merged a 14-commit refactor moving every Filament resource to schema definitions. -1.2k LOC, +40% load speed.',
                'external_url' => 'https://github.com/grdzelo',
                'posted_at' => now()->subDays(15),
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 3,
            ],
            [
                'platform' => 'instagram',
                'media_url' => 'https://instagram.com/p/demo-4',
                'thumbnail_url' => null,
                'caption' => 'Tbilisi old town, 35mm, ISO 200. Sometimes the best frame is just patience.',
                'external_url' => 'https://instagram.com/p/demo-4',
                'posted_at' => now()->subDays(22),
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 4,
            ],
            [
                'platform' => 'linkedin',
                'media_url' => null,
                'thumbnail_url' => null,
                'caption' => 'Wrote up the FINA mTLS handshake gotchas — saved me weeks of integration grief, hopefully saves yours too.',
                'external_url' => 'https://linkedin.com/in/grdzelo/posts/demo',
                'posted_at' => now()->subDays(34),
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($items as $item) {
            SocialFeedItem::create($item);
        }
    }

    private function seedContactSubmissions(): void
    {
        $items = [
            [
                'name' => 'Anna Beridze',
                'email' => 'anna@kavkasiagroup.ge',
                'company' => 'Kavkasia Group',
                'subject' => 'FINA integration — fintech onboarding',
                'budget_range' => '10-25k',
                'project_type' => 'integration',
                'message' => 'Hi Giorgi — we are launching a regulated savings product in Q3 and need a partner for the FINA integration. Read your post on fina-sdk-laravel and would love to talk.',
                'source' => 'contact_form',
                'status' => 'new',
                'ip_address' => '5.9.123.45',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
            [
                'name' => 'David Park',
                'email' => 'david@orbitlabs.io',
                'company' => 'Orbit Labs',
                'subject' => 'SaaS analytics rebuild',
                'budget_range' => '25-50k',
                'project_type' => 'rebuild',
                'message' => 'We need to migrate a Rails dashboard to something maintainable. Laravel + Filament looks like a fit. Are you taking on Q2 work?',
                'source' => 'contact_form',
                'status' => 'read',
                'ip_address' => '76.12.45.8',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Sara Mendes',
                'email' => 'sara@artisanmarket.pt',
                'company' => 'Artisan Marketplace',
                'subject' => 'Phase 2 — vendor analytics',
                'budget_range' => '5-10k',
                'project_type' => 'feature',
                'message' => 'Loved Phase 1. We want to add vendor analytics + payout reporting. Same scope of work — happy to bring you back in.',
                'source' => 'contact_form',
                'status' => 'replied',
                'replied_at' => now()->subDays(4),
                'ip_address' => '188.47.12.9',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(4),
            ],
            [
                'name' => 'Buy cheap watches now',
                'email' => 'noreply@spam-domain.xyz',
                'company' => null,
                'subject' => 'Special offer',
                'budget_range' => null,
                'project_type' => null,
                'message' => 'CHEAP REPLICA WATCHES VISIT NOW http://spam-domain.xyz',
                'source' => 'contact_form',
                'status' => 'spam',
                'ip_address' => '203.0.113.99',
                'user_agent' => 'curl/7.74',
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'name' => 'Marcus Lindgren',
                'email' => 'marcus@nordpay.se',
                'company' => 'Nordpay',
                'subject' => 'Architecture consultation',
                'budget_range' => '5-10k',
                'project_type' => 'consultation',
                'message' => 'We are pre-launch and need a second pair of eyes on our event-sourcing setup. 4-6 hour engagement.',
                'source' => 'contact_form',
                'status' => 'replied',
                'replied_at' => now()->subDays(15),
                'ip_address' => '83.252.4.7',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(15),
            ],
        ];

        foreach ($items as $item) {
            ContactSubmission::create($item);
        }
    }

    private function seedExchangeRates(): void
    {
        $today = CarbonImmutable::today();

        $pairs = [
            ['from' => 'USD', 'to' => 'GEL', 'rate' => 2.7150],
            ['from' => 'EUR', 'to' => 'GEL', 'rate' => 2.9420],
            ['from' => 'GEL', 'to' => 'USD', 'rate' => 0.3683],
            ['from' => 'GEL', 'to' => 'EUR', 'rate' => 0.3399],
            ['from' => 'USD', 'to' => 'EUR', 'rate' => 0.9229],
            ['from' => 'EUR', 'to' => 'USD', 'rate' => 1.0836],
        ];

        for ($i = 0; $i < 30; $i++) {
            $date = $today->subDays($i);
            foreach ($pairs as $pair) {
                $jitter = 1 + ((mt_rand(-150, 150) / 10000));
                ExchangeRate::create([
                    'from_currency' => $pair['from'],
                    'to_currency' => $pair['to'],
                    'rate' => round($pair['rate'] * $jitter, 8),
                    'rate_date' => $date->toDateString(),
                    'source' => 'nbg',
                ]);
            }
        }
    }

    private function seedExpenseCategories(): void
    {
        $categories = [
            ['title' => 'Software & SaaS', 'slug' => 'software-saas', 'icon' => 'heroicon-o-cloud', 'color' => '#1BA85A', 'sort_order' => 1],
            ['title' => 'Hosting & Infrastructure', 'slug' => 'hosting-infrastructure', 'icon' => 'heroicon-o-server', 'color' => '#3B82F6', 'sort_order' => 2],
            ['title' => 'Hardware', 'slug' => 'hardware', 'icon' => 'heroicon-o-computer-desktop', 'color' => '#8B5CF6', 'sort_order' => 3],
            ['title' => 'Office & Utilities', 'slug' => 'office-utilities', 'icon' => 'heroicon-o-home', 'color' => '#F59E0B', 'sort_order' => 4],
            ['title' => 'Travel', 'slug' => 'travel', 'icon' => 'heroicon-o-paper-airplane', 'color' => '#EF4444', 'sort_order' => 5],
            ['title' => 'Education & Books', 'slug' => 'education-books', 'icon' => 'heroicon-o-book-open', 'color' => '#10B981', 'sort_order' => 6],
            ['title' => 'Taxes & Accounting', 'slug' => 'taxes-accounting', 'icon' => 'heroicon-o-receipt-percent', 'color' => '#6B7280', 'sort_order' => 7],
            ['title' => 'Other', 'slug' => 'other', 'icon' => 'heroicon-o-ellipsis-horizontal-circle', 'color' => '#94A3B8', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::create($cat);
        }
    }

    private function seedExpenses(): void
    {
        $cats = ExpenseCategory::all()->keyBy('slug');
        $today = CarbonImmutable::today();

        $samples = [
            // Recurring monthly software
            ['cat' => 'software-saas', 'title' => 'GitHub Pro', 'amount' => 21, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => 'JetBrains All Products Pack', 'amount' => 24.90, 'currency' => 'EUR', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => '1Password Family', 'amount' => 4.99, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => 'Figma Professional', 'amount' => 15, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => 'Notion Plus', 'amount' => 10, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => 'Linear Standard', 'amount' => 8, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'software-saas', 'title' => 'Anthropic Claude Pro', 'amount' => 20, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],

            // Hosting
            ['cat' => 'hosting-infrastructure', 'title' => 'DigitalOcean droplets', 'amount' => 48, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'hosting-infrastructure', 'title' => 'Cloudflare Pro', 'amount' => 25, 'currency' => 'USD', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'hosting-infrastructure', 'title' => 'AWS S3 + CloudFront', 'amount' => 12.40, 'currency' => 'USD', 'recurring' => false],
            ['cat' => 'hosting-infrastructure', 'title' => 'Domain renewal — grdzelo.com', 'amount' => 14.99, 'currency' => 'USD', 'recurring' => false],

            // Office & Utilities
            ['cat' => 'office-utilities', 'title' => 'Internet — March', 'amount' => 89, 'currency' => 'GEL', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'office-utilities', 'title' => 'Electricity — March', 'amount' => 142, 'currency' => 'GEL', 'recurring' => true, 'interval' => 'monthly'],
            ['cat' => 'office-utilities', 'title' => 'Coworking day pass', 'amount' => 30, 'currency' => 'GEL', 'recurring' => false],

            // Hardware
            ['cat' => 'hardware', 'title' => 'External SSD 2TB', 'amount' => 189, 'currency' => 'EUR', 'recurring' => false],
            ['cat' => 'hardware', 'title' => 'Mechanical keyboard', 'amount' => 220, 'currency' => 'USD', 'recurring' => false],

            // Education
            ['cat' => 'education-books', 'title' => 'Designing Data-Intensive Applications', 'amount' => 42, 'currency' => 'USD', 'recurring' => false],
            ['cat' => 'education-books', 'title' => 'Laracon recording bundle', 'amount' => 99, 'currency' => 'USD', 'recurring' => false],

            // Travel
            ['cat' => 'travel', 'title' => 'Flight — TBS to LIS', 'amount' => 412, 'currency' => 'EUR', 'recurring' => false],
            ['cat' => 'travel', 'title' => 'Coworking week — Lisbon', 'amount' => 180, 'currency' => 'EUR', 'recurring' => false],
            ['cat' => 'travel', 'title' => 'Tbilisi → Mestia bus', 'amount' => 45, 'currency' => 'GEL', 'recurring' => false],

            // Taxes
            ['cat' => 'taxes-accounting', 'title' => 'Accountant fee — quarterly', 'amount' => 250, 'currency' => 'GEL', 'recurring' => true, 'interval' => 'quarterly'],
            ['cat' => 'taxes-accounting', 'title' => 'IE turnover tax — Q1', 'amount' => 1820, 'currency' => 'GEL', 'recurring' => true, 'interval' => 'quarterly'],
        ];

        foreach ($samples as $idx => $sample) {
            $cat = $cats[$sample['cat']] ?? null;
            $currency = Currency::from($sample['currency']);

            $rate = (float) (ExchangeRate::getRate($sample['currency'], 'GEL') ?? 1.0);
            $baseAmount = round($sample['amount'] * $rate, 2);

            // Spread expenses across the last 3 months for recurring, scattered for one-offs.
            if (! empty($sample['recurring'])) {
                for ($m = 0; $m < 3; $m++) {
                    $date = $today->startOfMonth()->subMonths($m)->addDays(mt_rand(0, 27));
                    Expense::create([
                        'expense_category_id' => $cat?->id,
                        'title' => $sample['title'],
                        'amount' => $sample['amount'],
                        'currency' => $currency,
                        'exchange_rate' => $rate,
                        'base_amount' => $baseAmount,
                        'base_currency' => 'GEL',
                        'date' => $date->toDateString(),
                        'is_recurring' => true,
                        'interval' => $sample['interval'] ?? 'monthly',
                        'note' => null,
                    ]);
                }
            } else {
                $date = $today->subDays(mt_rand(2, 80));
                Expense::create([
                    'expense_category_id' => $cat?->id,
                    'title' => $sample['title'],
                    'amount' => $sample['amount'],
                    'currency' => $currency,
                    'exchange_rate' => $rate,
                    'base_amount' => $baseAmount,
                    'base_currency' => 'GEL',
                    'date' => $date->toDateString(),
                    'is_recurring' => false,
                    'interval' => null,
                    'note' => $idx % 4 === 0 ? 'Reimbursable.' : null,
                ]);
            }
        }
    }

    private function seedIncomeSources(): void
    {
        $today = CarbonImmutable::today();

        $sources = [
            [
                'attrs' => [
                    'title' => 'Freelance — Kavkasia Group retainer',
                    'type' => IncomeType::Freelance,
                    'is_recurring' => true,
                    'amount' => 4500,
                    'currency' => Currency::USD,
                    'start_date' => $today->subMonths(8)->toDateString(),
                    'end_date' => null,
                    'interval' => 'monthly',
                    'expected_day' => 5,
                    'notes' => 'Retainer agreement — 30 hrs/month, FINA + finance work.',
                    'is_active' => true,
                ],
                'months' => 8,
                'status' => 'mostly_received',
            ],
            [
                'attrs' => [
                    'title' => 'Freelance — Orbit Labs project',
                    'type' => IncomeType::Project,
                    'is_recurring' => false,
                    'amount' => 18000,
                    'currency' => Currency::USD,
                    'start_date' => $today->subMonths(4)->toDateString(),
                    'end_date' => $today->subMonths(1)->toDateString(),
                    'interval' => null,
                    'expected_day' => null,
                    'notes' => 'Three milestones, all delivered.',
                    'is_active' => false,
                ],
                'months' => null,
                'status' => 'received',
                'milestones' => [
                    ['amount' => 6000, 'days_ago' => 110, 'received' => true],
                    ['amount' => 6000, 'days_ago' => 70, 'received' => true],
                    ['amount' => 6000, 'days_ago' => 30, 'received' => true],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'Apartment rental — Tbilisi flat',
                    'type' => IncomeType::Rent,
                    'is_recurring' => true,
                    'amount' => 800,
                    'currency' => Currency::GEL,
                    'start_date' => $today->subYears(2)->toDateString(),
                    'end_date' => null,
                    'interval' => 'monthly',
                    'expected_day' => 1,
                    'notes' => 'Long-term tenant, paid on the 1st.',
                    'is_active' => true,
                ],
                'months' => 6,
                'status' => 'all_received',
            ],
            [
                'attrs' => [
                    'title' => 'Employer salary — TechCorp',
                    'type' => IncomeType::Salary,
                    'is_recurring' => true,
                    'amount' => 6500,
                    'currency' => Currency::GEL,
                    'start_date' => $today->subYears(3)->toDateString(),
                    'end_date' => $today->subMonths(6)->toDateString(),
                    'interval' => 'monthly',
                    'expected_day' => 25,
                    'notes' => 'Wound down when transitioned to full-time freelance.',
                    'is_active' => false,
                ],
                'months' => null,
                'status' => 'archived',
            ],
            [
                'attrs' => [
                    'title' => 'Affiliate / passive — open-source sponsors',
                    'type' => IncomeType::Passive,
                    'is_recurring' => true,
                    'amount' => 95,
                    'currency' => Currency::USD,
                    'start_date' => $today->subYear()->toDateString(),
                    'end_date' => null,
                    'interval' => 'monthly',
                    'expected_day' => 15,
                    'notes' => 'GitHub Sponsors trickle.',
                    'is_active' => true,
                ],
                'months' => 6,
                'status' => 'mostly_received',
            ],
        ];

        foreach ($sources as $entry) {
            $source = IncomeSource::create($entry['attrs']);

            $rate = (float) (ExchangeRate::getRate($source->currency->value, 'GEL') ?? 1.0);

            if (! empty($entry['milestones'])) {
                foreach ($entry['milestones'] as $m) {
                    $date = $today->subDays($m['days_ago']);
                    IncomeEntry::create([
                        'income_source_id' => $source->id,
                        'date' => $date->toDateString(),
                        'amount' => $m['amount'],
                        'currency' => $source->currency,
                        'exchange_rate' => $rate,
                        'base_amount' => round($m['amount'] * $rate, 2),
                        'base_currency' => 'GEL',
                        'is_received' => $m['received'],
                        'received_at' => $m['received'] ? $date->addDays(2)->toDateTimeString() : null,
                        'note' => 'Milestone payment',
                    ]);
                }

                continue;
            }

            $monthsBack = $entry['months'] ?? 0;
            for ($i = 0; $i < $monthsBack; $i++) {
                $expectedDate = $today->startOfMonth()->subMonths($i)->addDays(($source->expected_day ?? 1) - 1);

                if ($expectedDate->greaterThan($today)) {
                    continue;
                }

                $isReceived = match ($entry['status']) {
                    'all_received' => true,
                    'mostly_received' => $i > 0 || mt_rand(0, 1) === 1,
                    default => false,
                };

                $amount = (float) $source->amount;

                IncomeEntry::create([
                    'income_source_id' => $source->id,
                    'date' => $expectedDate->toDateString(),
                    'amount' => $amount,
                    'currency' => $source->currency,
                    'exchange_rate' => $rate,
                    'base_amount' => round($amount * $rate, 2),
                    'base_currency' => 'GEL',
                    'is_received' => $isReceived,
                    'received_at' => $isReceived
                        ? $expectedDate->addDays(mt_rand(0, 3))->toDateTimeString()
                        : null,
                    'note' => null,
                ]);
            }
        }
    }

    private function seedSalaryRecords(): void
    {
        $today = CarbonImmutable::today();

        $techcorpSource = IncomeSource::where('title', 'Employer salary — TechCorp')->first();
        $kavkasiaSource = IncomeSource::where('title', 'Freelance — Kavkasia Group retainer')->first();

        $records = [
            [
                'income_source_id' => $techcorpSource?->id,
                'employer' => 'TechCorp',
                'gross_amount' => 6500,
                'currency' => Currency::GEL,
                'tax_percentage' => 20,
                'pay_frequency' => PayFrequency::Monthly,
                'payment_day' => 25,
                'start_date' => $today->subYears(3)->toDateString(),
                'end_date' => $today->subMonths(6)->toDateString(),
                'is_active' => false,
                'notes' => 'Standard 20% IT tax on payroll.',
            ],
            [
                'income_source_id' => $kavkasiaSource?->id,
                'employer' => 'Kavkasia Group (retainer)',
                'gross_amount' => 4500,
                'currency' => Currency::USD,
                'tax_percentage' => 1,
                'pay_frequency' => PayFrequency::Monthly,
                'payment_day' => 5,
                'start_date' => $today->subMonths(8)->toDateString(),
                'end_date' => null,
                'is_active' => true,
                'notes' => 'Individual entrepreneur — 1% turnover tax regime.',
            ],
        ];

        foreach ($records as $rec) {
            $tax = SalaryRecord::calculateTax((float) $rec['gross_amount'], (float) $rec['tax_percentage']);
            SalaryRecord::create(array_merge($rec, [
                'tax_amount' => $tax['tax_amount'],
                'net_amount' => $tax['net_amount'],
            ]));
        }
    }

    private function seedSubscriptions(): void
    {
        $cats = ExpenseCategory::all()->keyBy('slug');
        $today = CarbonImmutable::today();

        $subs = [
            [
                'attrs' => [
                    'title' => 'GitHub Pro',
                    'provider' => 'GitHub',
                    'category' => 'software-saas',
                    'amount' => 21,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subYears(2)->toDateString(),
                    'next_billing_date' => $today->addDays(4)->toDateString(),
                    'renewal_day' => 4,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://github.com/pricing',
                    'notes' => null,
                    'reminders_enabled' => true,
                    'reminder_days_before' => 3,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 720],
                    ['type' => SubscriptionEventType::Renewed, 'days_ago' => 60],
                    ['type' => SubscriptionEventType::Renewed, 'days_ago' => 30],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'JetBrains All Products Pack',
                    'provider' => 'JetBrains',
                    'category' => 'software-saas',
                    'amount' => 24.90,
                    'currency' => Currency::EUR,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subYears(3)->toDateString(),
                    'next_billing_date' => $today->addDays(11)->toDateString(),
                    'renewal_day' => 11,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://jetbrains.com',
                    'notes' => 'Year-3 loyalty discount applied.',
                    'reminders_enabled' => true,
                    'reminder_days_before' => 5,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 1095],
                    ['type' => SubscriptionEventType::PriceChanged, 'days_ago' => 380, 'old' => 22.90, 'new' => 24.90],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'DigitalOcean — production droplets',
                    'provider' => 'DigitalOcean',
                    'category' => 'hosting-infrastructure',
                    'amount' => 48,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subMonths(14)->toDateString(),
                    'next_billing_date' => $today->addDays(2)->toDateString(),
                    'renewal_day' => 2,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://digitalocean.com',
                    'notes' => 'Two droplets + managed db.',
                    'reminders_enabled' => true,
                    'reminder_days_before' => 2,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 425],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'Notion Plus',
                    'provider' => 'Notion',
                    'category' => 'software-saas',
                    'amount' => 10,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subYear()->toDateString(),
                    'next_billing_date' => null,
                    'renewal_day' => null,
                    'status' => SubscriptionStatus::Paused,
                    'auto_renew' => false,
                    'paused_at' => $today->subDays(20)->toDateTimeString(),
                    'website_url' => 'https://notion.so',
                    'notes' => 'Paused while migrating to Obsidian.',
                    'reminders_enabled' => false,
                    'reminder_days_before' => 3,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 365],
                    ['type' => SubscriptionEventType::Paused, 'days_ago' => 20],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'Spotify Family',
                    'provider' => 'Spotify',
                    'category' => null,
                    'amount' => 17.99,
                    'currency' => Currency::EUR,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subYears(4)->toDateString(),
                    'next_billing_date' => null,
                    'renewal_day' => null,
                    'status' => SubscriptionStatus::Cancelled,
                    'auto_renew' => false,
                    'cancelled_at' => $today->subMonths(2)->toDateTimeString(),
                    'ended_at' => $today->subMonths(2)->toDateString(),
                    'website_url' => 'https://spotify.com',
                    'notes' => 'Cancelled — switched to Apple Music.',
                    'reminders_enabled' => false,
                    'reminder_days_before' => 3,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 1460],
                    ['type' => SubscriptionEventType::Cancelled, 'days_ago' => 60],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'AWS — S3 + CloudFront',
                    'provider' => 'Amazon Web Services',
                    'category' => 'hosting-infrastructure',
                    'amount' => 12.40,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subMonths(20)->toDateString(),
                    'next_billing_date' => $today->addDays(6)->toDateString(),
                    'renewal_day' => 6,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://aws.amazon.com',
                    'notes' => 'Image hosting + CDN.',
                    'reminders_enabled' => false,
                    'reminder_days_before' => 3,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 600],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'Anthropic Claude Pro',
                    'provider' => 'Anthropic',
                    'category' => 'software-saas',
                    'amount' => 20,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Monthly,
                    'start_date' => $today->subMonths(7)->toDateString(),
                    'next_billing_date' => $today->addDays(9)->toDateString(),
                    'renewal_day' => 9,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://claude.ai',
                    'notes' => 'Daily driver.',
                    'reminders_enabled' => true,
                    'reminder_days_before' => 3,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 210],
                    ['type' => SubscriptionEventType::Renewed, 'days_ago' => 30],
                ],
            ],
            [
                'attrs' => [
                    'title' => 'Adobe Lightroom Photo Plan',
                    'provider' => 'Adobe',
                    'category' => 'software-saas',
                    'amount' => 11.99,
                    'currency' => Currency::USD,
                    'billing_interval' => BillingInterval::Yearly,
                    'start_date' => $today->subMonths(11)->toDateString(),
                    'next_billing_date' => $today->addDays(28)->toDateString(),
                    'renewal_day' => 28,
                    'status' => SubscriptionStatus::Active,
                    'auto_renew' => true,
                    'website_url' => 'https://adobe.com',
                    'notes' => 'Photography only.',
                    'reminders_enabled' => true,
                    'reminder_days_before' => 7,
                ],
                'events' => [
                    ['type' => SubscriptionEventType::Started, 'days_ago' => 335],
                ],
            ],
        ];

        foreach ($subs as $sub) {
            $attrs = $sub['attrs'];

            if (isset($attrs['category'])) {
                $attrs['expense_category_id'] = $cats[$attrs['category']]->id ?? null;
                unset($attrs['category']);
            }

            $subscription = Subscription::create($attrs);

            foreach ($sub['events'] as $event) {
                SubscriptionEvent::create([
                    'subscription_id' => $subscription->id,
                    'event_type' => $event['type'],
                    'old_amount' => $event['old'] ?? null,
                    'new_amount' => $event['new'] ?? null,
                    'note' => null,
                    'metadata' => null,
                    'occurred_at' => $today->subDays($event['days_ago'])->toDateTimeString(),
                ]);
            }

            if ($subscription->reminders_enabled && $subscription->next_billing_date) {
                $remindOn = $subscription->next_billing_date
                    ->copy()
                    ->subDays($subscription->reminder_days_before ?? 3);

                SubscriptionReminder::create([
                    'subscription_id' => $subscription->id,
                    'remind_on' => $remindOn->toDateString(),
                    'type' => 'renewal',
                    'channel' => 'mail',
                    'is_sent' => $remindOn->lessThan($today),
                    'sent_at' => $remindOn->lessThan($today) ? $remindOn->toDateTimeString() : null,
                ]);
            }
        }
    }
}
