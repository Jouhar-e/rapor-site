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
