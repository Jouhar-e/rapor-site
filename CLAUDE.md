# PKBM Academic Information System (Rapor Site)

Laravel 13 + Filament 5 admin panel for PKBM (Paket A/B/C equivalency programs). **Not a greenfield project** — the admin panel is fully built with 20+ Resources, 15+ Pages, 8+ Widgets, and custom Dashboard.

## Stack

| Tool | Version |
|------|---------|
| PHP | 8.3+ (prod), 8.5 (env) |
| Laravel | 13 |
| Filament | 5 |
| Tailwind CSS | 4 |
| Pest | 4 |
| Pint | 1 |
| Vite | 8 |
| DB (dev) | MySQL, database `rapor_site` |
| DB (test) | SQLite `:memory:` |

## Commands

| Purpose | Command |
|---------|---------|
| Initial setup | `composer run setup` (copies `.env`, generates key, migrates, installs npm, builds) |
| Dev server | `composer run dev` (runs `php artisan serve` + queue:listen + `npm run dev` concurrently) |
| Run all tests | `php artisan test --compact` or `composer run test` |
| Single test | `php artisan test --compact --filter=testName` |
| Create a test | `php artisan make:test --pest SomeFeatureTest` |
| Format PHP | `vendor/bin/pint --dirty --format agent` |
| Build frontend | `npm run build` |
| Route list | `php artisan route:list` |

## Key Config & Structure

- **`.env`** — APP_KEY set, DB MySQL, session/queue/cache use `database` driver
- **`phpunit.xml`** — testing uses SQLite in-memory with `RefreshDatabase`
- **`vite.config.js`** — Tailwind v4 plugin + Bunny Fonts (Instrument Sans)
- **`resources/css/app.css`** — Tailwind v4 `@import 'tailwindcss'` syntax (NOT v3 `@tailwind` directives)
- **Routes**: `routes/web.php` (minimal), `routes/console.php` (only `inspire`)
- **Filament Panel**: `app/Providers/Filament/AdminPanelProvider.php` — custom nav groups, colors, font, sidebar width
- **Dashboard**: `app/Filament/Pages/Dashboard.php` — 12-col grid (`xl:12`), role-based widgets, filter form
- **Widgets**: `app/Filament/Widgets/` — WelcomeWidget, AdminStatsCardsWidget, ProgressNilaiWidget, ProgressAbsensiWidget, GradeDistributionChart, AdminCharts, RecentActivityWidget, QuickActionsWidget, TutorStatsOverview
- **Resources**: 20+ Filament Resources under `app/Filament/Resources/` grouped by nav group
- **Pages**: 15+ custom Pages under `app/Filament/Pages/` (Reports, Imports, Management)
- **Models**: Under `app/Models/` with relationships, scopes, accessors
- **Services**: `app/Services/DashboardService.php` — stats, charts, progress logic

## Navigation Groups (sidebar order)

1. **AKADEMIK** — Learners, Classes, Programs, Tutors, Subjects, AcademicYears, Semesters, Phases, SubjectGroups, Extracurriculars, ClassLearners, HomeroomTeachers, HomeroomNotes, LearnerExtracurriculars, PromotionMappings, PromotionWizard
2. **PENILAIAN** — Grades, GradePredicates
3. **ABSENSI** — Attendances
4. **LAPORAN** — ReportGrades, ReportLearners, ReportAttendances, ReportPromotions, ReportExtracurriculars, ReportTutors
5. **PENGATURAN** — Users, BackupHistories, AuditLogs, ManageSchoolProfile, ManageGradingSettings

## App-Specific Notes

- **Filament 5** is installed (not v4 as old docs said)
- **Spatie Laravel Permission** is installed and used (roles: `admin`, `tutor`)
- Domain skills in `.agents/skills/`, `.claude/skills/`, `.cursor/skills/` — activate when working in those domains
- Expected architecture: Service Class Pattern, Laravel Policies, Eloquent API Resources
- Use `laravel-boost` MCP tools for DB queries, schema inspection, URL resolution, docs search
- **Always run Pint** before finalizing PHP changes: `vendor/bin/pint --dirty --format agent`

## Testing Conventions

- Pest 4 with `tests/Feature/` and `tests/Unit/`
- Use `RefreshDatabase` trait (SQLite in-memory)
- Use model factories for test data; check for custom states first
- Feature tests preferred over unit tests
- Test file naming: `SomeFeatureTest.php` (no suite directory in name)

## Frontend Quirks

- Tailwind v4 syntax only (`@import 'tailwindcss'`)
- If Vite manifest error: run `npm run build` or `npm run dev`
- Instrument Sans font via Bunny Fonts

## Git

- Remote: `origin` → `https://github.com/Jouhar-e/rapor-site.git`
- Branch: `main`
- Commit after each change set (user preference)