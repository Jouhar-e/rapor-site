# TASK: REDESIGN DASHBOARD PKBM NURUL JADID USING FILAMENT 5

Anda adalah Senior Laravel 12 + Filament 5 Developer dan UI/UX Designer.

Redesign dashboard PKBM Nurul Jadid agar terlihat seperti ERP/SIAKAD modern.

JANGAN membuat dashboard seperti kumpulan widget statistik biasa.

Gunakan:
- Laravel 12
- Filament 5
- TailwindCSS
- Heroicons
- Chart.js bawaan Filament

---

# GENERAL RULES

1. Gunakan layout dashboard modern.
2. Gunakan card dengan:
   - rounded-xl
   - shadow-sm
   - border
   - padding besar
3. Gunakan whitespace yang cukup.
4. Hindari tabel panjang.
5. Hindari widget kosong.
6. Gunakan warna soft.
7. Dashboard harus responsive.

---

# GRID SYSTEM

Gunakan grid 12 kolom.

```php
public function getColumns(): int|array
{
    return [
        'sm' => 1,
        'md' => 2,
        'xl' => 12,
    ];
}
```

---

# LAYOUT DASHBOARD

Susunan widget HARUS seperti berikut:

```text
WELCOME
████████████████████████████

STATISTIC
████ ████ ████ ████

PROGRESS
████████████ ████████████

ANALYTICS
████ ████ ████

QUICK ACTION
████████████████████████████
```

---

# WELCOME WIDGET

Gunakan:

```php
->columnSpanFull()
```

Tinggi maksimal:

```css
max-height: 140px;
```

Tampilkan:

- Avatar
- Nama user
- Role
- Tahun ajaran
- Semester
- Tanggal
- Jam

Tampilan:

```text
┌────────────────────────────────────┐
│ 👋 Selamat Datang, Admin PKBM      │
│ Tahun Ajaran 2026/2027             │
│ Semester Ganjil                    │
│ Selasa, 30 Juni 2026               │
│ 10:56 WIB                          │
└────────────────────────────────────┘
```

JANGAN membuat card tinggi.

---

# STATISTIC WIDGET

Gunakan:

```php
StatsOverviewWidget
```

Tampilkan 4 card:

- Total Tutor
- Total Warga Belajar
- Warga Aktif
- Total Alumni

Setiap card:

```php
->columnSpan(3)
```

Contoh:

```text
┌────────────┐
│ Tutor      │
│ 2          │
│ +1 bulan   │
└────────────┘
```

---

# PROGRESS WIDGET

Buat 2 widget.

## Progress Penilaian

```php
->columnSpan(6)
```

Tampilkan:

```text
Progress Penilaian

80%

███████████████░░

120 selesai
30 belum lengkap
0 belum mulai
```

Gunakan progress bar.

---

## Progress Absensi

```php
->columnSpan(6)
```

Tampilkan:

```text
Progress Absensi

65%

███████████░░░░░

90 selesai
50 belum lengkap
0 belum mulai
```

Gunakan progress bar.

---

# ANALYTICS

Layout:

```php
ProgramChart
    ->columnSpan(4);

NilaiChart
    ->columnSpan(4);

ActivityWidget
    ->columnSpan(4);
```

---

## Distribusi Program

Gunakan:

```php
BarChartWidget
```

Contoh:

```text
Paket A ████████
Paket B ████
Paket C ██
```

---

## Distribusi Nilai

Gunakan:

```php
DoughnutChartWidget
```

Kategori:

- A
- B
- C
- D

---

## Aktivitas Terbaru

JANGAN gunakan table panjang.

Gunakan list card.

Contoh:

```text
● Pak Ahmad input nilai
  2 jam lalu

● Bu Lina input absensi
  3 jam lalu

● Admin backup database
  5 jam lalu
```

Maksimal tampil 5 item.

---

# QUICK ACTION

Widget HARUS full width.

```php
->columnSpanFull()
```

Gunakan grid:

```text
┌──────┐
│ ✏️   │
│Nilai │
└──────┘

┌──────┐
│ 📅   │
│Absen │
└──────┘

┌──────┐
│ 🖨️   │
│Rapor │
└──────┘
```

Buat 6 action:

- Input Nilai
- Input Absensi
- Cetak Rapor
- Tambah Warga Belajar
- Kelola Tutor
- Backup Sistem

JANGAN tampilkan sebagai daftar teks.

---

# HAPUS

Jangan tampilkan:

- Backup terakhir
- Total backup
- Notifikasi kosong
- Pie chart sederhana
- Tabel aktivitas panjang
- Widget kosong
- Card terlalu tinggi

---

# TARGET VISUAL

Dashboard harus menyerupai:

- ERP Sekolah Modern
- SIAKAD Profesional
- AdminLTE modern
- Filament Pro dashboard
- BUKAN dashboard CRUD standar

---

# OUTPUT

Buat:

1. Widget Filament 5
2. Grid layout
3. Column span
4. Chart widget
5. Progress widget
6. Quick action widget
7. Activity widget
8. Responsive dashboard
9. UI modern dan minimalis