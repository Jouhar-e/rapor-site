# PKBM Academic Information System - Master PRD Final

## 1. Project Overview

Sistem Informasi Akademik PKBM berbasis web untuk:
- Paket A
- Paket B
- Paket C

Tujuan utama:
- Mengelola data akademik PKBM
- Mengelola penilaian
- Mengelola administrasi pembelajaran
- Menghasilkan data rapor (modul rapor dikerjakan pada fase berikutnya)

---

## 2. Technology Stack

- Laravel 12
- PHP 8.3+
- MySQL
- Filament 4
- Tailwind CSS
- Spatie Laravel Permission
- Laravel Policy
- Migration
- Seeder
- Factory
- Service Class Pattern

---

## 3. Development Workflow

1. Analisis Sistem
2. UI/UX Design
3. Sitemap & User Flow
4. ERD
5. Database Design
6. Backend Development
7. Testing
8. Modul Rapor

---

## 4. Roles

### Admin
Memiliki akses penuh.
Admin dapat merangkap sebagai wali kelas.

### Tutor/Wali Kelas
- Login menggunakan email
- Satu tutor dapat menjadi wali lebih dari satu kelas
- Hanya dapat mengakses kelas yang diampu

---

## 5. Authentication

### Login Tutor
- Username: Email
- Password default: pkbm12345

### Profile
Tutor dapat:
- Mengubah password
- Mengubah foto
- Mengubah telepon
- Mengubah alamat

Tutor tidak dapat:
- Mengubah email
- Mengubah NIP
- Mengubah role

---

## 6. Profil PKBM

Table: school_profiles

- id
- name
- npsn
- address
- district
- city
- province
- postal_code
- phone
- email
- website
- logo
- headmaster_name
- headmaster_nip
- headmaster_signature
- school_stamp
- description

---

## 7. Program PKBM

- Paket A
- Paket B
- Paket C

Table: programs

- id
- code
- name
- description
- is_active

---

## 8. Tahun Ajaran

Table: academic_years

- id
- name
- start_date
- end_date
- is_active
- is_archived

Rules:
- Hanya satu tahun ajaran aktif.
- Tahun ajaran arsip hanya dapat diakses admin.

---

## 9. Semester

Table: semesters

- id
- academic_year_id
- name
- is_active

Rules:
- Semester ganjil dan genap dapat aktif bersamaan.

---

## 10. Tutor

Table: tutors

- id
- user_id
- nip
- name
- gender
- birth_place
- birth_date
- address
- phone
- email
- photo
- is_active

---

## 11. Warga Belajar

Table: learners

- id
- program_id
- nis
- nisn
- name
- gender
- birth_place
- birth_date
- address
- status

Status:
- aktif
- lulus
- pindah
- keluar
- alumni

---

## 12. Kelas

Nama kelas bersifat dinamis.

Contoh:
- Paket A Kelas 6
- Paket B Reguler
- Paket C IPS
- Paket C Malam

Table: classes

- id
- program_id
- name
- description
- status

Status:
- aktif
- tidak_aktif
- lulus
- arsip

---

## 13. Wali Kelas

Table: homeroom_teachers

- id
- tutor_id
- class_id
- academic_year_id

---

## 14. Penempatan Warga Belajar

Table: class_learners

- id
- learner_id
- class_id
- academic_year_id

---

## 15. Mata Pelajaran

Berdasarkan program dan kelas.

Table: subjects

- id
- program_id
- class_id
- code
- name
- description
- is_active

---

## 16. Ekstrakurikuler

Dikelola admin.

Table: extracurriculars

- id
- code
- name
- description
- is_active

---

## 17. Penilaian

Komponen:
- Tugas
- PTS
- PAS
- Praktik

Table: grades

- id
- learner_id
- subject_id
- academic_year_id
- semester_id
- task_score
- pts_score
- pas_score
- practice_score
- final_score
- predicate
- description
- status

Status:
- draft
- published
- locked

---

## 18. Pengaturan Penilaian

Table: grading_settings

- id
- task_percentage
- pts_percentage
- pas_percentage
- practice_percentage
- min_score
- max_score
- rounding_digits

Rules:
- Total bobot = 100%

---

## 19. Predikat

Table: grade_predicates

- id
- min_score
- max_score
- predicate
- description

Deskripsi otomatis tetapi masih dapat diedit.

Default:

A:
Memiliki kemampuan sangat baik dalam memahami materi.

B:
Memiliki kemampuan baik dalam memahami materi.

C:
Memiliki kemampuan cukup dalam memahami materi.

D:
Memerlukan bimbingan lebih lanjut.

---

## 20. Absensi

Per semester.

Table: attendances

- id
- learner_id
- academic_year_id
- semester_id
- sick
- permission
- absent

---

## 21. Ekstrakurikuler Warga Belajar

Table: learner_extracurriculars

- id
- learner_id
- extracurricular_id
- academic_year_id
- semester_id
- grade
- description

---

## 22. Catatan Wali Kelas

Table: homeroom_notes

- id
- learner_id
- academic_year_id
- semester_id
- note

---

## 23. Import Export CSV

Import:
- Tutor
- Warga Belajar
- Nilai

Export:
- Nilai

Template dibuat otomatis.

### Tutor
nip,name,email,phone

### Warga Belajar
nis,nisn,name,gender,birth_place,birth_date,address,status

### Nilai
nis,name,subject_task,subject_pts,subject_pas,subject_practice

Strategi:
- Sama -> Skip
- Berubah -> Update
- Baru -> Insert

Semua import memiliki:
- Preview
- Validation
- Error report
- Import history

---

## 24. Publish dan Lock Nilai

Flow:

Draft
→ Published
→ Locked

Draft:
- Tutor dapat edit.

Published:
- Siap digunakan.

Locked:
- Tidak dapat diedit tutor.
- Hanya admin dapat unlock.

---

## 25. Kenaikan Kelas

Menggunakan mapping manual.

Table: promotion_mappings

- id
- source_class_id
- destination_class_id

Workflow:
1. Buat tahun ajaran baru.
2. Buat mapping.
3. Generate kenaikan.
4. Review.
5. Edit manual.
6. Konfirmasi.
7. Arsipkan tahun lama.
8. Aktifkan tahun baru.

---

## 26. Dashboard

### Admin

Statistics:
- Total tutor
- Total warga belajar
- Total warga aktif
- Total alumni
- Total kelas
- Total mapel
- Total wali kelas
- Total nilai draft
- Total nilai published
- Total nilai locked

Charts:
- Program PKBM
- Status warga belajar
- Distribusi kelas
- Kelulusan
- Kelengkapan nilai
- Kelengkapan absensi

Widgets:
- Aktivitas terbaru
- Notifikasi
- Backup terakhir
- Tahun ajaran aktif

### Tutor

Statistics:
- Total kelas diampu
- Total warga belajar
- Nilai belum lengkap
- Absensi belum lengkap

Widgets:
- Daftar kelas
- Aktivitas
- Notifikasi

---

## 27. Notifikasi

Admin:
- Tahun ajaran belum aktif
- Semester belum dibuat
- Nilai belum lengkap
- Backup belum dilakukan
- Kenaikan belum diproses

Tutor:
- Nilai belum lengkap
- Absensi belum lengkap
- Catatan belum diisi
- Nilai belum dipublish

---

## 28. Laporan

- Laporan tutor
- Laporan warga belajar
- Laporan wali kelas
- Laporan nilai
- Laporan absensi
- Laporan ekstrakurikuler
- Laporan kenaikan kelas
- Laporan status warga belajar

---

## 29. Backup & Restore

Backup:
- Database
- Upload
- Logo
- Tanda tangan
- Stempel
- Konfigurasi

Restore:
- Database
- File
- Full restore

Table: backup_histories

- id
- file_name
- file_size
- backup_type
- created_by
- created_at

---

## 30. Audit Log

Table: audit_logs

- id
- user
- role
- ip_address
- browser
- url
- method
- action
- table_name
- record_id
- old_value
- new_value
- created_at

Dicatat:
- Login
- Logout
- Create
- Update
- Delete
- Import
- Export
- Backup
- Kenaikan kelas
- Publish nilai
- Lock nilai

---

## 31. Permission Matrix

### Admin
Full Access.

### Tutor
- Dashboard: View
- Kelas: View sendiri
- Warga Belajar: View sendiri
- Nilai: CRUD sendiri
- Absensi: CRUD sendiri
- Ekstrakurikuler: CRUD sendiri
- Catatan: CRUD sendiri
- Laporan: View sendiri

---

## 32. UI/UX

Prinsip:
- Clean
- Modern
- Professional
- Responsive
- Desktop First

Design System:
- Font: Inter
- Primary: Blue
- Success: Green
- Warning: Yellow
- Danger: Red
- Info: Cyan

Komponen:
- Button
- Input
- Select
- Checkbox
- Card
- Table
- Modal
- Alert
- Toast
- Tabs
- Pagination
- Statistics Card

---

## 33. Sitemap

MASTER DATA
- Program
- Tahun Ajaran
- Semester
- Tutor
- Warga Belajar
- Kelas
- Mata Pelajaran
- Ekstrakurikuler

AKADEMIK
- Wali Kelas
- Penempatan
- Nilai
- Absensi
- Ekstrakurikuler
- Catatan
- Kenaikan Kelas

IMPORT EXPORT
- Import Tutor
- Import Warga Belajar
- Import Nilai
- Export Nilai

LAPORAN
- Tutor
- Warga Belajar
- Nilai
- Absensi
- Ekstrakurikuler

SISTEM
- Dashboard
- Audit Log
- Backup
- Pengaturan

---

## 34. Final ERD Tables

- users
- roles
- permissions
- school_profiles
- programs
- academic_years
- semesters
- tutors
- learners
- classes
- homeroom_teachers
- class_learners
- subjects
- extracurriculars
- grades
- grade_predicates
- grading_settings
- attendances
- learner_extracurriculars
- homeroom_notes
- promotion_mappings
- notifications
- audit_logs
- backup_histories

---

## 35. Modul Ditunda

Belum dibuat:
- Template rapor
- Preview rapor
- Cetak rapor
- Cetak massal
- Format PDF rapor

Ketika dibuat:
- PDF
- Tanda tangan manual
- Template Paket A
- Template Paket B
- Template Paket C

---

## 36. Expected Output

1. Analisis Sistem
2. UI/UX
3. Sitemap
4. User Flow
5. ERD
6. Database Design
7. Migration
8. Model
9. Seeder
10. Factory
11. Policy
12. Filament Resource
13. Service Class
14. Dashboard Widget
15. Roadmap
16. TODO List
