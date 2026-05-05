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

### `App\Settings\GeneralSettings` (Spatie settings class)

| Field | Legacy property | Type | Storage dir | Has value today | HasMedia today |
|---|---|---|---|---|---|
| About portrait | `about_image` | `?string` | `storage/app/public/settings/` | no | **n/a — settings classes do not implement HasMedia** |

- **Target collection / model:** Settings classes are not Eloquent models — they cannot host `addMediaCollection()`. The prompt's mention of `GeneralSettings` consolidating to media-library is technically a misclassification. **Two clean paths Task 2 must pick from; surfaces here for the reviewer to ratify:**
  - **(a)** Keep `about_image` as a `string` setting; replace the Filament `FileUpload` with a media-library uploader **on a dedicated singleton `Brand` model** that holds the about portrait (and, going forward, any other bytes-on-disk settings). Adds one model + one migration. Consistent with the rest of the batch (uniform media-library access).
  - **(b)** Leave `about_image` as a `string` setting permanently. Skip from this consolidation. The Vue page reads `settings.about_image` directly today (per `About.vue:194` via `BasePublicController::sharedProps`); 006's responsive variants would have to be generated by a separate non-media-library pipeline for this single asset.
- **Recommendation:** **(a)**. Cost is small (one tiny `brands` table with `id` and timestamps, or even an `is_singleton` constraint on a generic `branding_assets` table), benefit is uniform read paths in 006. The brand model can host other singleton brand assets later (`logo`, `logo_dark`, `favicon`) if the same problem arises for those.
- **Resolve before Task 2 starts.** Block the user on (a)/(b) decision; flagged in the Task 1 hand-off notes below.
- **Filament form path today:** `app/Filament/Pages/ManageGeneralSettings.php:60`.
- **Public read site:** `BasePublicController::sharedProps` → all public pages (in shared props). Used by `About.vue:194`. Alt is `t('about.portrait_alt')` from `lang/{en,ka,ru}.json` — stays put, per prompt.

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
- **Models touched (5 amended, 2 new contract):**
  - `app/Models/Project.php` — `registerMediaCollections()`, dual-read accessors.
  - `app/Models/Article.php` — same.
  - `app/Models/Hobby.php` — same; legacy column is `image` not `cover_image`.
  - `app/Models/Album.php` — adds `implements HasMedia` + `InteractsWithMedia` + `registerMediaCollections()` + accessors.
  - `app/Models/Certification.php` — same as Album.
  - `app/Models/Repository.php` — adds `cover` collection (parallel to existing `screenshots`); thumbnail accessor delegates to it.
  - **(if §a chosen for GeneralSettings)** `app/Models/Brand.php` — new singleton model with `HasMedia`. New migration `create_brands_table.php`.
- **Filament resources / pages touched (6):** `ProjectResource.php`, `ArticleResource.php`, `HobbyResource.php`, `AlbumResource.php`, `CertificationResource.php`, `RepositoryResource.php`. **(if §a)** `ManageGeneralSettings.php` rewires `about_image` to read/write through the Brand model.
- **Tests:** `tests/Feature/Media/UploaderSwapTest.php` (new). One assertion per resource for uploader-class-swap; one round-trip per accessor for dual-read fallback.

### Task 3 — `media:ingest-public` artisan command
- `app/Console/Commands/MediaIngestPublic.php` — new. Iterates models in scope, calls `addMedia(...)->preservingOriginal()->toMediaCollection(...)`. `--dry-run`, `--reverse`, `--model=`, `--collection=`, `--force`.
- `tests/Feature/Console/MediaIngestPublicTest.php` — forward + reverse + idempotence + originals-byte-identical.

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
  - `drop_about_image_from_general_settings.php` — settings DB row update, not a schema migration. Or, if §a chosen, no drop here at all (the setting is already gone post-Task 2 because Brand model superseded it).
- **`RepositoryResource.php`** — add `SpatieMediaLibraryFileUpload::make('screenshots')->collection('screenshots')->multiple()->reorderable()` field + per-locale alt tab strip.
- **Alt translatability:** Filament uploaders' per-file alt input becomes a per-locale tab strip writing to `media.custom_properties->alt = ['en' => ..., 'ka' => ..., 'ru' => ...]`. Accessor on a small `Media` extension or via `getRegisteredMediaCollections` macro returns active-locale alt with default-locale fallback.
- **Tests:** `tests/Feature/Admin/TranslatableCoverageTest.php` extended (from 004); `tests/Feature/Media/AltTranslatabilityTest.php` new.

---

## Reconciliation flags before Task 2 starts

1. **`GeneralSettings::about_image` cannot host a media collection.** Settings classes are not Eloquent models. Pick:
   - **(a)** New singleton `Brand` model owns `about_image` (recommended).
   - **(b)** Leave `about_image` as a string setting; skip from media-library consolidation; 006 handles it via a non-media-library variant pipeline for this single asset.
2. **Album and Certification gain `HasMedia` from scratch in Task 2** — call out in commit message; not just "amending an existing contract".
3. **`Album.photos` is `string[]`, not `{path, caption}[]`** — confirmed against live data. Task 3's caption-preservation logic is a defensive no-op for current data; the test suite asserts the no-op shape.
4. **`Hobby` legacy hero column is `image` not `cover_image`** — naming mismatch is the single most error-prone spot in Task 2's accessor shim and Task 5's column drop. Both must reference `image`.
5. **Repository `thumbnail`** is being consolidated into a new `cover` collection on Repository — not into the existing `screenshots` collection. Task 2's commit must register both collections.
6. **Decorative collections** (Certification `badge`) are still consolidated into media-library, but flagged in code as "no responsive variants in 006" so 006's `MediaConversions::registerFor()` skips them by collection name.

**Task 2 cannot start until reconciliation flag (1) is resolved.** Flags (2)–(6) are documented decisions baked into the plan above; reviewers can ack via PR comment or by approving this doc commit.

---

## Rollback runbook

In order, executed against the affected branch / environment:

1. `php artisan media:ingest-public --reverse` — restores legacy column values from `media.file_name` + `custom_properties->source_path` for every in-scope row.
2. `php artisan migrate:rollback --step=N` where N covers Task 5's column-drop migrations. Brings the columns back as nullable string/JSON.
3. (Optional, if data integrity damaged) Restore from the latest backup. Backups are operator-provided; this batch does not introduce a backup mechanism.

The reverse path is exercised in `tests/Feature/Console/MediaIngestPublicTest.php` against a known-good fixture before Task 5's column drops are merged.
