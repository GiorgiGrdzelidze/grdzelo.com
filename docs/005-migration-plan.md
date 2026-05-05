# 005 — Media library consolidation: migration plan

> **Status.** Task 1 artifact for `prompts/005-medialibrary-consolidation.md`. Doc-only — no code changes in this commit. This document is the binding contract for Tasks 2–5; reviewers approve the columns map here once, then Tasks 2–5 must execute against it without redrawing scope. Deleted alongside the prompt at end-of-batch (per the prompt's hand-off section).
>
> **Pre-flight reference.** Inputs are the site-analyzer map from the previous /005 attempt (now superseded by `prompts/006-image-perf-conversions.md`) and a fresh tinker pass on local dev. No drift detected since the map was produced.
>
> **Row counts.** Pulled from local dev (Sail, MySQL 8.4) on 2026-05-05. Production counts must be re-run via `php artisan tinker` against the live DB **before** Task 3 (`media:ingest-public`) is executed in production. The local dev dataset is sparse seed data — real volume sizing is the production read.

---

## Invariants (apply to every entry below)

- **Disk:** all conversions go to the `public` disk (`storage/app/public/...`, served at `/storage/...` via the `php artisan storage:link` symlink). Spatie media library's default (`config/media-library.php`) already uses this disk; no `->useDisk(...)` override is needed in `registerMediaCollections()`.
- **Files preserved.** Task 3's backfill calls `addMedia($legacyPath)->preservingOriginal()->toMediaCollection($collection)`. The legacy file at `storage/app/public/<dir>/<file>` stays byte-identical until **Task 5's column-drop migration**. Even Task 5 only drops the *column* — orphaned legacy files on disk are removed in a separate documented operator step (`php artisan storage:prune-legacy` or by hand), not in any migration.
- **Replace deletes previous.** Where a Filament uploader produces a single-file collection, `->singleFile()` is set on `registerMediaCollections()` so a re-upload deletes the prior media row's file (Spatie default). For multi-file collections, the operator deletes manually.
- **Reversibility runbook.** Every column-drop migration's `down()` re-adds the column as nullable and is intentionally **not auto-backfilled**. Operator rollback path: run `php artisan migrate:rollback --step=N`, then `php artisan media:ingest-public --reverse` to repopulate the columns from `media.file_name` / `media.custom_properties->source_path`. The reverse path is exercised in Task 3's tests.
- **Alts (Task 5).** Image alt is stored on `media.custom_properties->alt` as a translatable JSON shape `{en, ka, ru}`. Read via a small accessor that delegates to `Spatie\Translatable` semantics: `alt[app()->getLocale()] ?? alt[Locale::default()] ?? null`. The Filament uploader's per-file alt input is a per-locale tab strip — same pattern as 004's translatable contract.
- **Out of scope models** (mentioned for clarity, **not** in this migration): `Page`, `Testimonial`, `SocialFeedItem` already implement `HasMedia` but render no public `<img>`. Their existing wiring is left untouched. `Skill::icon` is an external URL or devicon class — not a stored file. `Service` has no images. `SeoSettings::default_og_image`, `default_twitter_image`, and `GeneralSettings::logo` / `logo_dark` / `logo_icon` / `favicon` are admin/SEO branding — not consumed by the consolidated collections in this batch (left as-is, per the prompt's "binding list" §Models in scope). Per-resource SEO `og_image` / `twitter_image` (on Project, Article, Hobby, Album, Repository, Service, Page) are handled by the existing `HasSeoFields` pipeline and stay out of scope here — moving them into media-library is a follow-up if `og:image` ever needs responsive variants (it doesn't, per spec).

---

## Per-model columns map

### `App\Models\Project`

| Field | Legacy column | Type | Nullable | Storage dir | Live rows total | Rows w/ value | Total file entries | HasMedia today |
|---|---|---|---|---|---|---|---|---|
| Cover | `cover_image` | `VARCHAR(255)` | yes | `storage/app/public/projects/` | 3 | 0 | 0 | yes (vestigial) |
| Gallery | `gallery` | `JSON` (array of path strings) | yes | `storage/app/public/projects/gallery/` | 3 | 0 | 0 | yes (vestigial) |

- **Target collections:**
  - `cover` — single-file. Replace deletes previous.
  - `gallery` — multi-file, reorderable.
- **Migration source:** `database/migrations/2026_03_30_110001_create_projects_table.php:30-31`.
- **Filament form path:** `app/Filament/Resources/ProjectResource.php:137` (cover_image), `:144` (gallery). Both in the "Media" tab/section.
- **Public read sites today (replaced in Task 4):**
  - Cover: `app/Http/Controllers/Public/HomeController.php` Inertia prop → `Public/Home.vue:259`. `app/Http/Controllers/Public/ProjectController.php@index,show` → `Projects/Index.vue:98`, `Projects/Show.vue:192`.
  - Gallery: `ProjectController@show` → `Projects/Show.vue:395`.
- **Dual-read accessor names (Task 2 introduces, Task 4 simplifies to media-only):**
  - `getCoverImageUrlAttribute(): ?string` — returns `getFirstMediaUrl('cover') ?: ($cover_image ? Storage::disk('public')->url($cover_image) : null)`. Vue prop name stays `cover_image` initially, then renamed to `cover` in Task 4 (or controller emits `cover_image` keyed to the media URL — either is fine; map says rename for clarity).
  - `getGalleryUrlsAttribute(): array` — returns `getMedia('gallery')->map(fn($m) => ['url' => $m->getUrl(), 'alt' => $m->getCustomProperty('alt')])->all()` ?: legacy mapping over `gallery[]`. Final shape post-Task 4: `[{url, alt}]`.
- **`registerMediaCollections()` declaration (Task 2):**
  ```php
  public function registerMediaCollections(): void
  {
      $this->addMediaCollection('cover')->singleFile();
      $this->addMediaCollection('gallery');
  }
  ```
- **Notes:** No legacy `caption` field on `gallery[]` (the JSON is a flat array of path strings, not `{path, caption}`). The site-analyzer's earlier note "JSON `[{path, caption, ...}]`" was inferred from the prompt rather than confirmed against schema; corrected here. `custom_properties->caption` on the media row is **only** introduced for `Album.photos` (which does have captions in the schema below).

### `App\Models\Article`

| Field | Legacy column | Type | Nullable | Storage dir | Live rows total | Rows w/ value | HasMedia today |
|---|---|---|---|---|---|---|---|
| Cover | `cover_image` | `VARCHAR(255)` | yes | `storage/app/public/articles/` | 3 | 0 | yes (vestigial) |

- **Target collection:** `cover` — single-file.
- **Migration source:** `database/migrations/2026_03_30_110007_create_articles_table.php:17`.
- **Filament form path:** `app/Filament/Resources/ArticleResource.php:91`.
- **Public read sites today:** `ArticleController@index,show` → `Blog/Index.vue:121`, `Blog/Show.vue:307`, `Blog/Show.vue:381` (related articles).
- **Dual-read accessor:** `getCoverImageUrlAttribute(): ?string`.
- **`registerMediaCollections()`:** `$this->addMediaCollection('cover')->singleFile();`

### `App\Models\Hobby`

| Field | Legacy column | Type | Nullable | Storage dir | Live rows total | Rows w/ value | Total file entries | HasMedia today |
|---|---|---|---|---|---|---|---|---|
| Hero image | `image` | `VARCHAR(255)` | yes | `storage/app/public/hobbies/` | 5 | 0 | 0 | yes (vestigial) |
| Gallery | `gallery` | `JSON` (array of path strings) | yes | `storage/app/public/hobbies/gallery/` | 5 | 0 | 0 | yes (vestigial) |

- **Target collections:**
  - `cover` — single-file (renamed from the legacy field name `image` to align the collection naming convention with Project / Article / Album).
  - `gallery` — multi-file, reorderable.
- **Migration source:** `database/migrations/2026_03_30_110008_create_hobbies_table.php:19` (gallery only — `image` is on the create-hobbies table or a follow-up; verify in Task 2's pre-amble).
- **Filament form path:** `app/Filament/Resources/HobbyResource.php:49` (`image`), `:50` (`gallery`).
- **Public read sites:** `AboutController@hobbies,hobby` → `Hobbies.vue:148`, `Hobbies/Show.vue:67` (hero), `Hobbies/Show.vue:90` (gallery iter).
- **Dual-read accessors:** `getCoverImageUrlAttribute()` (legacy fallback reads `$this->image`, NOT `$this->cover_image` — naming mismatch is the most error-prone spot in this batch). `getGalleryUrlsAttribute()`.
- **`registerMediaCollections()`:**
  ```php
  $this->addMediaCollection('cover')->singleFile();
  $this->addMediaCollection('gallery');
  ```
- **Note on column rename:** the legacy column is `image`, not `cover_image`. Task 5's drop migration is `drop_image_from_hobbies.php`. The dual-read accessor must read `$this->image` (not `$this->cover_image`) — this is called out explicitly because it's the only model whose legacy-column name differs from the convention.

### `App\Models\Album`

| Field | Legacy column | Type | Nullable | Storage dir | Live rows total | Rows w/ value | Total file entries | HasMedia today |
|---|---|---|---|---|---|---|---|---|
| Cover | `cover_image` | `VARCHAR(255)` | yes | `storage/app/public/albums/covers/` | 4 | 0 | 0 | **no** |
| Photos | `photos` | `JSON` (array of path strings) | yes | `storage/app/public/albums/photos/` (e.g., `albums/photos/demo/kazbegi-01.jpg`) | 4 | 3 | 10 | **no** |

- **Target collections:**
  - `cover` — single-file.
  - `photos` — multi-file, reorderable.
- **Migration source:** `database/migrations/2026_03_30_130002_create_albums_table.php:17-18`.
- **Filament form path:** `app/Filament/Resources/AlbumResource.php:80` (cover_image), `:86` (photos).
- **Public read sites:** `GalleryController@index,show` → `Gallery/Index.vue:88,211` (cover); `Gallery/Show.vue:192,272` (photos + lightbox). Controller currently emits `Storage::url(...)` against each path.
- **Dual-read accessors:** `getCoverImageUrlAttribute()`, `getPhotoEntriesAttribute(): array` (returns `[{url, caption, alt}]`).
- **`registerMediaCollections()`:**
  ```php
  $this->addMediaCollection('cover')->singleFile();
  $this->addMediaCollection('photos');
  ```
- **Important: Album does NOT implement `HasMedia` today.** Task 2 must:
  1. Add `implements HasMedia` to the class signature.
  2. `use InteractsWithMedia` in the trait list.
  3. Implement `registerMediaCollections()` for the first time.
  This is the only in-scope model that gains the contract from scratch (others have it vestigially); call it out in the Task 2 commit message.
- **Caption preservation.** The site-analyzer map and the prompt both stated Album.photos was `JSON [{path, caption, ...}]`. **Live data shows a flat array of path strings** (`["albums/photos/demo/kazbegi-01.jpg", …]`). Captions are NOT currently stored per-photo — Album-level fields (e.g. `Album.title`, `Album.description`) substitute at render time.
  - **Implication for Task 3 (`media:ingest-public`):** the per-photo `caption` ingest the prompt describes is a **no-op** for current data — there's nothing to carry into `custom_properties->caption`. The command implementation can still write `custom_properties->caption = $entry['caption'] ?? null` defensively in case the JSON shape varies in production, but the test suite should assert: ingesting a flat-string-array `photos` value writes media rows with `custom_properties->caption = null`. **No reconciliation question for the user — the prompt's `{path, caption}` shape does not exist in this DB; defensive ingestion handles both.**

### `App\Models\Certification`

| Field | Legacy column | Type | Nullable | Storage dir | Live rows total | Rows w/ value | HasMedia today |
|---|---|---|---|---|---|---|---|
| Badge | `badge_image` | `VARCHAR(255)` | yes | `storage/app/public/certifications/` | 3 | 0 | **no** |

- **Target collection:** `badge` — single-file.
- **Migration source:** `database/migrations/2026_03_30_120005_create_certifications_table.php:21`.
- **Filament form path:** `app/Filament/Resources/CertificationResource.php:46`.
- **Public read sites:** `AboutController@about,certifications` → `Certifications.vue:73`. (Also referenced from the About page's certifications block — verify in Task 4.)
- **Dual-read accessor:** `getBadgeImageUrlAttribute(): ?string`.
- **`registerMediaCollections()`:** `$this->addMediaCollection('badge')->singleFile();`
- **Important: Certification does NOT implement `HasMedia` today** — same gain-from-scratch path as Album.
- **Decorative size.** `Certifications.vue:73` renders this at `h-14 w-14` (56 CSS px). Per `prompts/005-medialibrary-consolidation.md` §Models in scope, the storage consolidation still happens here, but **006 will skip responsive variants for this collection** (decorative chrome ≤96 px). Document the flag on the model:
  ```php
  // Decorative collection — no responsive variants in 006.
  $this->addMediaCollection('badge')->singleFile();
  ```

### `App\Settings\GeneralSettings` → new `App\Models\Brand` (singleton)

| Field | Legacy property | Type | Storage dir | Has value today |
|---|---|---|---|---|
| About portrait | `GeneralSettings::about_image` | `?string` | `storage/app/public/settings/` | no |

**Resolved.** Spatie settings classes are not Eloquent models, so `GeneralSettings` cannot host `addMediaCollection()`. A new singleton **`App\Models\Brand`** Eloquent model carries every binary-asset setting going forward; `about_image` is its first occupant. The setting is removed from `GeneralSettings` in Task 5 via a Spatie settings migration (Spatie settings have their own migration mechanism — see `database/settings/`).

- **Target collection / model:** `App\Models\Brand` (Eloquent), single row, `about` collection (`->singleFile()`).
- **Schema migration (Task 2 introduces):** `database/migrations/<ts>_create_brands_table.php`.
  ```php
  Schema::create('brands', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
  });
  ```
  Deliberately bare. The Brand row carries no scalar columns — every asset is a media-library entry on a named collection. Future singleton brand assets (`logo`, `logo_dark`, `favicon`, `og_default_image`) get added as new collections on the same model, no schema change required.
- **Settings migration (Task 5 removes the legacy property):** `database/settings/<ts>_remove_about_image_from_general_settings.php` (Spatie settings format). Down migration re-adds the setting as nullable.
- **Singleton enforcement.** A `current()` static on the model creates-on-first-call:
  ```php
  public static function current(): self
  {
      return static::firstOrCreate(['id' => 1]);
  }
  ```
  No factory / seeder needed — `current()` self-heals on any environment (dev, CI, production). Tests can call `Brand::current()` without setup.
- **`registerMediaCollections()` (Task 2):**
  ```php
  $this->addMediaCollection('about')->singleFile();
  ```
- **Filament admin location (decision: dedicated Page, same sidebar group as `ManageGeneralSettings`).**
  - **Choice:** new `App\Filament\Pages\ManageBranding` (extends `Filament\Pages\Page` + `HasForms`), bound to `Brand::current()` as the form record. Sidebar group: `Settings` (same as `ManageGeneralSettings`). Menu label: `Branding`. Shows up immediately under General Settings in the admin sidebar.
  - **Why dedicated, not embedded.** `ManageGeneralSettings` is a Spatie-settings-page (settings-class lifecycle, no Eloquent record). Embedding a media field bound to a different model (Brand) would require dual record binding on a single page — technically possible in Filament v5 but fragile. A dedicated `ManageBranding` page uses the standard `HasForms` + Eloquent record lifecycle (`mount()` → `$this->record = Brand::current()`, `save()` → standard Eloquent persist + media-library hooks). Admin discoverability is unchanged: same sidebar group, one extra menu item. Future binary-asset settings (`logo`, `logo_dark`, `favicon`) all land on this same page.
  - **Form schema:** a single `SpatieMediaLibraryFileUpload::make('about')->collection('about')->image()` — alt input becomes the per-locale tab strip pattern in Task 5.
- **Migration of existing data (Task 3, `media:ingest-public`):** if `app(GeneralSettings::class)->about_image` is non-empty, the command copies that one file into `Brand::current()` `about` collection. Idempotent — re-running is a no-op once the media row exists. The legacy setting value stays readable through Task 4's shim, then is removed by Task 5's settings migration.
- **Public read site:** `BasePublicController::sharedProps` → all public pages (shared prop). Used at `About.vue:194`.
  - **Task 2 (shim):** shared prop reads `Brand::current()->getFirstMediaUrl('about')` if non-empty, else falls back to `Storage::disk('public')->url($settings->about_image)`. Prop name stays `settings.about_image` to keep Vue side untouched.
  - **Task 4 (post-shim):** shared prop reads media URL only; setting value no longer consulted. Prop name may stay `settings.about_image` or migrate to `brand.about` — pick one and stay consistent (Task 4 picks).
- **Alt:** `t('about.portrait_alt')` from `lang/{en,ka,ru}.json` — stays put, per prompt. Brand's `about` collection's `media.custom_properties->alt` is **not** wired for this asset (the i18n string is the canonical alt source). Document this exception explicitly in Task 5's alt-translatability commit so the translatable-coverage test from 004 knows to skip Brand's `about` collection.

### `App\Models\Repository` — out-of-migration, in-Task-5

| Field | Legacy column / collection | Type | Nullable | Storage dir | Live rows total | Rows w/ value | HasMedia today |
|---|---|---|---|---|---|---|---|
| Thumbnail (single) | `thumbnail` | `VARCHAR(255)` | yes | n/a (string column with passthrough URL or `/storage/<path>` — see helper at `Repositories/Show.vue:73-87`) | 6 | 0 | yes |
| Screenshots (multi) | `screenshots` collection | media-library | n/a | media-table → `public` disk | 6 | 0 entries | yes |

- **Target:** `thumbnail` migrates to media-library `cover` collection on Repository (consistent with Project/Article naming). `screenshots` is **already** on media-library — only the missing **admin uploader** is added in Task 5 (per the prompt's binding list).
- **Filament form path today:** `app/Filament/Resources/RepositoryResource.php:114` (thumbnail). Screenshots uploader does not exist; Task 5 adds `SpatieMediaLibraryFileUpload::make('screenshots')->collection('screenshots')->multiple()->reorderable()`.
- **Public read sites:** `RepositoryController@index,show` → `Repositories/Show.vue:308` (thumbnail), `:431` (screenshots — already a media-library URL via `getMedia('screenshots')->each(...)`).
- **`registerMediaCollections()` (Task 2 amends):**
  ```php
  $this->addMediaCollection('cover')->singleFile();   // new in Task 2 (was the 'thumbnail' string column)
  $this->addMediaCollection('screenshots');           // already exists; preserved as-is
  ```

---

## Aggregate sizing snapshot (local dev, 2026-05-05)

```
projects.total = 3
projects.cover_image_set = 0
projects.gallery_nonempty = 0
projects.gallery_total_entries = 0

articles.total = 3
articles.cover_image_set = 0

hobbies.total = 5
hobbies.image_set = 0
hobbies.gallery_nonempty = 0
hobbies.gallery_total_entries = 0

albums.total = 4
albums.cover_image_set = 0
albums.photos_nonempty = 3
albums.photos_total_entries = 10        # only non-zero entry on local dev

certifications.total = 3
certifications.badge_image_set = 0

repositories.total = 6
repositories.thumbnail_set = 0
repositories.screenshots_media_count = 0

general_settings.about_image_set = 0
```

**Total media rows the local Task 3 backfill would create: 10** (all from `Album.photos`). Production will differ — the operator runs `php artisan media:ingest-public --dry-run` before the live ingest to size the real backfill.

**Disk reality.** `storage/app/public/` on local dev is empty apart from `.gitignore`. The 10 `Album.photos` paths reference files that do not exist locally (demo seeds reference `albums/photos/demo/kazbegi-0[1-4].jpg`). Implication for Task 3 tests: the test suite must construct fixtures (real bytes on the public disk) before exercising ingest, since tinker counts ≠ on-disk reality. **Production must be checked the same way before Task 3 runs there:** `php artisan media:ingest-public --dry-run` lists the *intended* row count; the operator should manually verify a sample of the listed paths exists on disk before running the live ingest.

---

## Per-task file inventory (preview — final list materialises in each task's commit)

### Task 2 — Filament uploader swap + dual-read shim
- **Models touched (4 amended, 3 new contract — including Brand):**
  - `app/Models/Project.php` — `registerMediaCollections()`, dual-read accessors.
  - `app/Models/Article.php` — same.
  - `app/Models/Hobby.php` — same; legacy column is `image` not `cover_image`.
  - `app/Models/Repository.php` — adds `cover` collection (parallel to existing `screenshots`); thumbnail accessor delegates to it.
  - `app/Models/Album.php` — adds `implements HasMedia` + `InteractsWithMedia` + `registerMediaCollections()` + accessors. **Gain-from-scratch.**
  - `app/Models/Certification.php` — same as Album. **Gain-from-scratch.**
  - `app/Models/Brand.php` — **new** singleton Eloquent model with `HasMedia`, `current()` static, `about` collection. **Gain-from-scratch.**
- **New migration:** `database/migrations/<ts>_create_brands_table.php` (id + timestamps only).
- **Filament resources / pages touched (6 amended, 1 new):**
  - `ProjectResource.php`, `ArticleResource.php`, `HobbyResource.php`, `AlbumResource.php`, `CertificationResource.php`, `RepositoryResource.php` — uploader fields swap to `SpatieMediaLibraryFileUpload::make('media')->collection('<collection>')`.
  - **New:** `app/Filament/Pages/ManageBranding.php` — dedicated Filament Page in `Settings` sidebar group, bound to `Brand::current()` as the form record. Form schema: a single `SpatieMediaLibraryFileUpload::make('about')->collection('about')->image()` for the about portrait.
  - `ManageGeneralSettings.php` — removes the `about_image` `FileUpload` from its schema. The `about_image` setting property stays on `GeneralSettings` until Task 5's settings migration.
- **Shared props (`BasePublicController::sharedProps`):** dual-read shim for `settings.about_image` reads `Brand::current()->getFirstMediaUrl('about')` if set, falls back to `Storage::disk('public')->url($settings->about_image)`. Prop name unchanged.
- **Tests:** `tests/Feature/Media/UploaderSwapTest.php` (new). One assertion per resource for uploader-class-swap; one round-trip per accessor for dual-read fallback. **Plus:** assertion that `Brand::current()` is idempotent (calling twice returns the same row); assertion that `ManageBranding` form renders a `SpatieMediaLibraryFileUpload` for the `about` collection.

### Task 3 — `media:ingest-public` artisan command
- `app/Console/Commands/MediaIngestPublic.php` — new. Iterates models in scope, calls `addMedia(...)->preservingOriginal()->toMediaCollection(...)`. `--dry-run`, `--reverse`, `--model=`, `--collection=`, `--force`.
- **`Brand` ingestion path:** if `app(GeneralSettings::class)->about_image` is non-empty AND `Brand::current()->getMedia('about')->isEmpty()`, copy the file at `storage/app/public/<about_image>` into `Brand::current()` `about` collection via `addMedia(...)->preservingOriginal()->toMediaCollection('about')`. Idempotent — re-runs after a successful ingest are no-ops because the second condition fails. Reverse path: with `--reverse`, write `media.file_name` (or `custom_properties->source_path`) back to `GeneralSettings::about_image` and persist the settings.
- `tests/Feature/Console/MediaIngestPublicTest.php` — forward + reverse + idempotence + originals-byte-identical. Plus: a Brand-specific test asserting the about_image setting is ingested into `Brand::current()` and that re-running is a no-op.

### Task 4 — public read-path migration
- **Controllers (8):** `HomeController`, `ProjectController`, `ArticleController`, `AboutController` (hobbies + certifications + about), `GalleryController`, `RepositoryController`, plus any others Task 1's map didn't surface — verify on the task's pre-amble.
- **Models:** dual-read accessors simplified to media-only.
- **Vue pages:** zero binding changes if controllers preserve prop names. **Verify** via grep: `cover_image`, `image`, `gallery[`, `photos[`, `badge_image`, `about_image` references in `resources/js/pages/Public/**` must all resolve to the new media-URL prop content — the prop *names* stay; their *contents* now come from media-library.
- **Tests:** `tests/Feature/Public/PublicImageReadPathTest.php` — Inertia prop matches `getFirstMediaUrl(...)`; null when no media; Album photo entry shape.

### Task 5 — column drops + Repository screenshots admin + alt translatability
- **Migrations (one per column drop, all reversible):**
  - `drop_cover_image_from_projects.php`
  - `drop_gallery_from_projects.php`
  - `drop_cover_image_from_articles.php`
  - `drop_image_from_hobbies.php` (note: column is `image`, not `cover_image`)
  - `drop_gallery_from_hobbies.php`
  - `drop_cover_image_from_albums.php`
  - `drop_photos_from_albums.php`
  - `drop_badge_image_from_certifications.php`
  - `drop_thumbnail_from_repositories.php`
  - `database/settings/<ts>_remove_about_image_from_general_settings.php` — **Spatie settings migration** (not a Schema migration). `up()` calls `$this->migrator->delete('general.about_image')`. `down()` re-adds with `$this->migrator->addEncrypted('general.about_image', null)` or `$this->migrator->add('general.about_image', null)` depending on the existing setting type. The Brand model already owns the asset by Task 5 (Task 2 introduced it; Task 3 ingested it; Task 4 cut the read path); this migration removes the dead setting property.
- **`RepositoryResource.php`** — add `SpatieMediaLibraryFileUpload::make('screenshots')->collection('screenshots')->multiple()->reorderable()` field + per-locale alt tab strip.
- **Alt translatability:** Filament uploaders' per-file alt input becomes a per-locale tab strip writing to `media.custom_properties->alt = ['en' => ..., 'ka' => ..., 'ru' => ...]`. Accessor on a small `Media` extension or via `getRegisteredMediaCollections` macro returns active-locale alt with default-locale fallback.
- **Tests:** `tests/Feature/Admin/TranslatableCoverageTest.php` extended (from 004); `tests/Feature/Media/AltTranslatabilityTest.php` new.

---

## Reconciliation flags (resolved)

1. **✅ Resolved — option (a) chosen.** New singleton `App\Models\Brand` Eloquent model owns `about_image` (and is the homing site for future singleton brand assets — `logo`, `logo_dark`, `favicon`, `og_default_image`). Filament admin: dedicated `App\Filament\Pages\ManageBranding` Page in the `Settings` sidebar group, bound to `Brand::current()`. Settings property `GeneralSettings::about_image` is removed via a Spatie settings migration in Task 5. See §`App\Settings\GeneralSettings` → new `App\Models\Brand` (singleton) above for the full spec.
2. **✅ Album and Certification gain `HasMedia` from scratch in Task 2** — called out in commit message; not just "amending an existing contract".
3. **✅ `Album.photos` is `string[]`, not `{path, caption}[]`** — confirmed against live data. Task 3's caption-preservation logic is a defensive no-op for current data; the test suite asserts the no-op shape. **Per the user's note on the 006 prompt: alts come exclusively from `media.custom_properties->alt` per-locale (set in 005 Task 5); when admin hasn't typed an alt, render `alt=""` rather than synthesizing a title-based fake.** Documented for downstream batch.
4. **✅ `Hobby` legacy hero column is `image` not `cover_image`** — Task 2's accessor shim reads `$this->image`; Task 5's drop migration is `drop_image_from_hobbies.php`. Both reference `image`.
5. **✅ Repository `thumbnail`** is being consolidated into a new `cover` collection on Repository — not into the existing `screenshots` collection. Task 2's commit registers both.
6. **✅ Decorative collections** (Certification `badge`) are still consolidated into media-library, but flagged in code as "no responsive variants in 006" so 006's `MediaConversions::registerFor()` skips them by collection name.

All six flags resolved. Task 2 may start.

---

## Rollback runbook

In order, executed against the affected branch / environment:

1. `php artisan media:ingest-public --reverse` — restores legacy column values from `media.file_name` + `custom_properties->source_path` for every in-scope row.
2. `php artisan migrate:rollback --step=N` where N covers Task 5's column-drop migrations. Brings the columns back as nullable string/JSON.
3. (Optional, if data integrity damaged) Restore from the latest backup. Backups are operator-provided; this batch does not introduce a backup mechanism.

The reverse path is exercised in `tests/Feature/Console/MediaIngestPublicTest.php` against a known-good fixture before Task 5's column drops are merged.
