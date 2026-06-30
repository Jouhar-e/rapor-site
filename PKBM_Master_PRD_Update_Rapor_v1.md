# PKBM_Master_PRD_Update_Rapor_v1

## Tujuan Update

Dokumen ini merupakan update terhadap `PKBM_Master_PRD_Final.md`.

Update ini dibuat berdasarkan analisis format rapor PKBM.

---

# 1. Penambahan Data Warga Belajar

Table: `learners`

Tambahkan field:

- religion
- child_order
- phone
- admission_date
- admission_class
- admission_status
- father_name
- father_job
- mother_name
- mother_job
- guardian_name
- guardian_job
- report_number

---

# 2. Penambahan Fase

Buat tabel `phases`:

- id
- code
- name
- description
- is_active

Contoh:
- A
- B
- C
- D
- E
- F

---

# 3. Update Table Classes

Tambahkan:

- phase_id

---

# 4. Kelompok Mata Pelajaran

Buat tabel `subject_groups`:

- id
- name
- description
- sort_order
- is_active

Contoh:
- Kelompok Mata Pelajaran Umum
- Kelompok Pemberdayaan
- Kelompok Keterampilan
- Muatan Lokal

---

# 5. Update Table Subjects

Tambahkan:

- subject_group_id

Relasi:

subjects belongsTo subject_groups

---

# 6. Update Table Grades

Tambahkan:

- competency_description (TEXT)

Digunakan untuk menyimpan capaian kompetensi pada rapor.

---

# 7. Template Capaian Kompetensi

Buat tabel `competency_templates`:

- id
- subject_id
- predicate
- achievement_text
- improvement_text
- created_at
- updated_at

Contoh:

A:
{nama} menunjukkan kompetensi yang sangat baik dalam memahami materi.

B:
{nama} menunjukkan kompetensi yang baik dalam memahami materi.

C:
{nama} menunjukkan kompetensi yang cukup dalam memahami materi dan perlu meningkatkan latihan.

D:
{nama} memerlukan pendampingan lebih lanjut.

---

# 8. Workflow Deskripsi Kompetensi

Input nilai
→ Hitung nilai akhir
→ Cari predikat
→ Cari template
→ Generate deskripsi otomatis
→ Tutor dapat edit manual
→ Simpan ke grades.competency_description

---

# 9. Update Ekstrakurikuler

Update table `learner_extracurriculars`:

- id
- learner_id
- extracurricular_id
- academic_year_id
- semester_id
- predicate
- description

Predikat:
- A
- B
- C
- D

---

# 10. Nomor Rapor

Buat tabel `learner_reports`:

- id
- learner_id
- academic_year_id
- semester_id
- report_number
- issued_date
- status
- created_at
- updated_at

---

# 11. Header Rapor

Data:
- Nama PKBM
- NPSN
- Alamat
- Nama Peserta Didik
- NIS
- NISN
- Nomor Rapor
- Kelas
- Fase
- Semester
- Tahun Ajaran

---

# 12. Identitas Peserta Didik

Data:
- Nama
- NIS/NISN
- Tempat Lahir
- Tanggal Lahir
- Jenis Kelamin
- Agama
- Anak Ke
- Telepon
- Alamat
- Nomor Gawai
- Tanggal Diterima
- Kelas Awal
- Status Masuk
- Nama Ayah
- Pekerjaan Ayah
- Nama Ibu
- Pekerjaan Ibu
- Nama Wali
- Pekerjaan Wali

---

# 13. Struktur Nilai Rapor

Kelompok Mata Pelajaran
→ Mata Pelajaran
→ Nilai Akhir
→ Capaian Kompetensi

---

# 14. Struktur Ekstrakurikuler

- Ekstrakurikuler
- Predikat
- Deskripsi

---

# 15. Struktur Absensi

- Izin
- Sakit
- Alpa

---

# 16. Struktur Tanda Tangan

- Orang Tua/Wali
- Kepala PKBM
- Wali Kelas

Tanda tangan: manual.

---

# 17. Status Modul Rapor

STATUS: DITUNDA

Implementasi dilakukan setelah:
- ERD final selesai
- Database final selesai
- Sistem akademik selesai
- Workflow nilai selesai
