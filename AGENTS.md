# PKBM Academic Information System (Rapor Site)

Laravel 13 + Filament 5 admin panel untuk program PKBM (Paket A/B/C). 21 Resource, 14 standalone Page, 10 Service.

## Stack

| Tool | Versi |
|------|-------|
| PHP | 8.3+ (prod), 8.5 (env) |
| Laravel | 13 |
| Filament | 5 |
| Tailwind CSS | 4 |
| Pest | 4 |
| Pint | 1 |
| Vite | 8 |
| DB (dev) | MySQL, database `rapor_site` |
| DB (test) | SQLite `:memory:` via `phpunit.xml` |

## Perintah Penting

| Tujuan | Perintah |
|--------|----------|
| Setup awal | `composer run setup` |
| Dev server | `composer run dev` (serve + queue:listen + Vite bareng) |
| Semua test | `php artisan test --compact` atau `composer run test` |
| Test tertentu | `php artisan test --compact --filter=testName` |
| Buat test | `php artisan make:test --pest SomeFeatureTest` |
| Format PHP | `vendor/bin/pint --dirty --format agent` |
| Build frontend | `npm run build` |
| Lihat routes | `php artisan route:list` |
| Rebuild DB + seeder | `php artisan migrate:fresh --seed` |
| Install Chrome untuk PDF | `npx puppeteer browsers install chrome@stable` |
| Push | `git push origin main` |

## Konfigurasi Kunci

- **`opencode.json`** — mengaktifkan `laravel-boost` MCP (`php artisan boost:mcp`) untuk query DB, cek schema, cari route/docs.
- **`.env`** — APP_KEY sudah set, DB MySQL, session/queue/cache pakai `database` driver.
- **`resources/css/app.css`** — Tailwind v4 (`@import 'tailwindcss'`), BUKAN v3 (`@tailwind`).
- **Routes**: `routes/web.php` cuma 4 route template-download. Selebihnya di-handle Filament.
- **`$shouldRegisterNavigation = false`** — semua `ImportXxx` dan `ExportGrade` disembunyikan dari sidebar, diakses via URL/tombol dari resource.

## Dependencies Penting

- **`spatie/browsershot`** (composer) + **puppeteer** (npm) — untuk generate PDF rapor.
- Chrome browser di-download via `npx puppeteer browsers install chrome@stable` ke `~/.cache/puppeteer/chrome/`.
- Path Chrome dideteksi otomatis di `ReportCardService::__construct()` dengan scan direktori cache.

## Arsitektur

- **Filament Panel**: `app/Providers/Filament/AdminPanelProvider.php` — nav group (`Akademik`, `Master Data`, `Laporan`, `Sistem`), warna Biru (Blue), font Instrument Sans, sidebar collapsible.
- **Resource** di `app/Filament/Resources/` — masing-masing punya table, form, dan 1+ page.
- **Tipe Page**:
  - `ManageXxx` — CRUD langsung dari Resource.
  - `ImportXxx` — upload XLSX + preview + import massal. XLSX-only. Tidak muncul di sidebar.
  - `ExportGrade` — export nilai per subject ke XLSX. Tidak muncul di sidebar.
  - `ReportXxx` / `CetakRapot` — form filter + export, hanya admin.
  - `Login` — custom login page dengan error message detail (akun tidak ditemukan / password salah / tidak punya akses).
  - `PromotionWizard` — wizard multi-step untuk kenaikan kelas.
- **Services** di `app/Services/`:
  - `ExcelService.php` — generasi XLSX
  - `ImportService.php` — logika import
  - `ReportCardService.php` — generate PDF rapor via Browsershot
  - `GradeService.php`, `AttendanceService.php`, `PromotionService.php` — logika domain
  - `DashboardService.php` — statistik/chart
  - `BackupService.php`, `AuditService.php`, `NotificationService.php` — utilitas
- **Model** di `app/Models/` — Eloquent model, relasi, scope, accessor.
- **Permissions**: Spatie Laravel Permission, role `admin` (semua) dan `tutor` (terbatas: CRUD grade/attendance/extracurricular/homeroom-note + import). Laporan hanya admin.

## Grup Navigasi (urutan sidebar)

### Akademik
sort=0: HomeroomNotes → ClassLearners → Grades → PromotionMappings → Attendances → LearnerExtracurriculars → HomeroomTeachers, lalu Page: PromotionWizard (sort=8), CetakRapot (sort=99)

### Master Data
Programs (sort=1) → AcademicYears (2) → Semesters (3) → Tutors (4) → Learners (5) → Classes (6) → Subjects (7) → SubjectGroups (8) → Extracurriculars (8) → Phases

### Laporan
ReportTutors (sort=1) → ReportLearners (2) → ReportGrades (3) → ReportAttendances (4) → ReportExtracurriculars (5) → ReportPromotions (6)

### Sistem
Users (sort=0) → AuditLogs (1) → BackupHistories (2) → GradePredicates (3) → ManageSchoolProfile (4) → ManageGradingSettings (5)

## Gotcha Kritis

### Filter + refresh table — dua pola berbeda

**Pola 1** (`statePath('filters')` — ReportTutors, ReportLearners, CetakRapot, ManageGradePivot):
```php
Select::make('semester_id')
    ->reactive()
    ->afterStateUpdated(fn () => $this->resetTable()),
```
Ditambah fallback Livewire hook:
```php
public function updated($propertyName): void { $this->resetTable(); }
```

**Pola 2** (component-bound state — ReportGrades, ReportAttendances, ReportExtracurriculars):
```php
public ?int $semester_id = null;
// ...
Select::make('semester_id')
    ->live()
    ->afterStateUpdated(fn () => $this->updatedSemesterId()),
public function updatedSemesterId(): void { $this->resetTable(); }
```

### `when($value, fn)` vs `filled($value)`

Di Filter Scout / SelectFilter, `$state = ['value' => null]` bukan null — pakai `filled($state['value'] ?? null)`. PHP anggap `'0'` sebagai falsy — pakai `filled($value)` bukan `when($value, fn)`.

### Browsershot / Chrome PDF

- Chrome path dideteksi otomatis di `ReportCardService::__construct()` dengan scan `~/.cache/puppeteer/chrome/`.
- WAJIB tambahkan `--no-sandbox` dan `--disable-gpu` via `addChromiumArguments()`.
- `setNodeBinary('C:\Program Files\nodejs\node.exe')` diperlukan di Windows.
- Kalau error `Could not find Chrome`, jalankan `npx puppeteer browsers install chrome@stable`.

### Import/Export: XLSX saja

Semua import pakai **PhpSpreadsheet `IOFactory`** untuk parse `.xlsx`. Export pakai `ExcelService::exportReport()`. CSV tidak didukung.

### Model Learner tidak punya `semester_id` / `academic_year_id`

Tabel `learners` tidak punya kolom semester. Filter berdasarkan semester/tahun ajaran via `classLearners`:
```php
->whereHas('classLearners', fn (Builder $q) => $q->where('semester_id', $v))
```

### Policy auto-discovery

Laravel Gate auto-mencari policy berdasarkan nama model (e.g. `Phase` → `PhasePolicy`). Jika file policy dihapus tanpa registrasi, error `Failed to open stream`. Solusi: hapus model atau daftarkan di `AuthServiceProvider::$policies`.

### Migration ordering

4 migration `fix`/`add` berjalan sebelum `create` table migration karena timestamp. Dilindungi dengan `Schema::hasTable()` guard. Jangan copy pattern ini — di Laravel 13 migration baru pakai timestamp manual.

### Database

Seeder bikin user `admin@pkbm.test` / `tutor@pkbm.test`. `php artisan migrate:fresh --seed` untuk rebuild.

## Konvensi Testing

- Pest 4 dengan trait `RefreshDatabase` (SQLite in-memory via `phpunit.xml`).
- Pakai model factory; cek custom states dulu.
- Feature test > unit test.
- Nama file: `SomeFeatureTest.php` (tanpa direktori suite).

## Frontend

- Tailwind v4 — pakai `@import 'tailwindcss'`, jangan `@tailwind`.
- Kalau error Vite manifest: `npm run build` atau `npm run dev`.
- Font Instrument Sans via Bunny Fonts.

## Git

- Remote: `origin` → `https://github.com/Jouhar-e/rapor-site.git`
- Branch: `main`
- Commit tiap selesai perubahan (preferensi user).
- **WAJIB**: jalankan `vendor/bin/pint --dirty --format agent` sebelum finalisasi perubahan PHP.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- filament/filament (FILAMENT) - v5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
