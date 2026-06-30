# PKBM_Master_PRD_Update_Rapor_PDF_v1

## Tujuan

Dokumen ini merupakan spesifikasi format cetak PDF rapor PKBM berdasarkan contoh rapor PKBM Nurul Jadid.

Modul rapor akan menggunakan:
- HTML Template
- CSS Print
- PDF Generator (disarankan: Spatie Browsershot + Chromium)

---

# 1. Konfigurasi Dokumen

Ukuran Kertas:
- A4
- Portrait

Margin:
- Atas: 15 mm
- Bawah: 15 mm
- Kiri: 20 mm
- Kanan: 15 mm

Font:
- Arial

Ukuran:
- Cover: 16 pt
- Judul: 14 pt
- Isi: 10 pt
- Tabel: 9 pt

---

# 2. Struktur Rapor

Halaman 1
- Cover

Halaman 2
- Identitas Sekolah

Halaman 3
- Biodata Peserta Didik

Halaman 4+
- Nilai Akademik
- Ekstrakurikuler
- Absensi
- Tanda Tangan

---

# 3. Cover Rapor

Bagian atas:
- Nomor Rapor
- NPSN

Bagian tengah:
- Logo PKBM
- Nama PKBM
- Judul rapor
- Program Paket

Bagian peserta:
- Nama Peserta Didik
- NIPD/NISN

Bagian bawah:
- Nama PKBM
- NPSN
- Alamat
- Telepon
- Email
- Website

---

# 4. Halaman Identitas Sekolah

Judul:
IDENTITAS SEKOLAH

Data:
- Nama Satuan Pendidikan
- NPSN
- Alamat
- Kode Pos
- Website
- Email
- Telepon

Footer:
- Tempat
- Tanggal
- Kepala PKBM
- Tanda tangan
- NIP

---

# 5. Halaman Biodata Peserta Didik

Judul:
KETERANGAN DIRI TENTANG PESERTA DIDIK

Data:
1. Nama
2. NIS/NISN
3. Tempat/Tanggal Lahir
4. Jenis Kelamin
5. Agama
6. Anak ke
7. Telepon
8. Alamat
9. Nomor Gawai
10. Diterima di sekolah
11. Nama Orang Tua
12. Pekerjaan Orang Tua
13. Nama Wali
14. Pekerjaan Wali

Footer:
- Tempat dan tanggal
- Kepala PKBM
- Tanda tangan
- NIP

---

# 6. Header Halaman Nilai

Header selalu tampil:

- Nama Satuan Pendidikan
- Alamat
- Kelas
- Semester
- Fase
- Tahun Ajaran
- Nama Peserta Didik
- NIS/NISN

---

# 7. Tabel Nilai Akademik

Kolom:

| No | Mata Pelajaran | Nilai Akhir | Capaian Kompetensi |

Pengelompokan:

- Kelompok Mata Pelajaran Umum
- Kelompok Pemberdayaan
- Kelompok Keterampilan
- Muatan Lokal

Aturan:
- Nilai rata tengah
- Deskripsi rata kiri
- Row otomatis bertambah
- Page break otomatis

---

# 8. Capaian Kompetensi

Format:

[NAMA] menunjukkan kompetensi yang baik dalam ...

[NAMA] perlu berlatih lagi dalam ...

Aturan:
- Dibuat otomatis
- Boleh diedit manual
- Disimpan permanen

---

# 9. Ekstrakurikuler

Kolom:

| No | Kegiatan Ekstrakurikuler | Predikat | Keterangan |

Predikat:
- A
- B
- C
- D

---

# 10. Absensi

Format:

| Keterangan | Jumlah |
| Izin | x hari |
| Sakit | x hari |
| Alpa | x hari |

---

# 11. Footer Nilai

Bagian bawah:

- Orang Tua/Wali
- Kepala PKBM
- Wali Kelas

Masing-masing:
- Nama
- Tanda tangan
- NIP

---

# 12. Tanda Tangan

Metode:
- Manual

Yang dicetak:
- Nama pejabat
- NIP
- Area tanda tangan

---

# 13. Penomoran Halaman

Format:

Halaman 1 : Cover
Halaman 2 : Identitas Sekolah
Halaman 3 : Biodata
Halaman 4+ : Nilai

---

# 14. CSS Print Rules

Gunakan:
- page-break-after
- page-break-before
- page-break-inside: avoid
- fixed header
- fixed footer

---

# 15. PDF Engine

Disarankan:

1. Spatie Browsershot + Chromium

Alternatif:
2. wkhtmltopdf
3. DOMPDF

---

# 16. Output

Generate:

- Preview HTML
- Preview PDF
- Download PDF
- Cetak PDF
- Cetak Massal PDF
