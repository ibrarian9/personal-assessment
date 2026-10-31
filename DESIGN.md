# Design Direction: PLA Merdeka (SMA Assessment Engine)

> Berkas arahan desain resmi sesuai standar **anti-slop** (`R-31`, `R-37`).
> Menetapkan identitas visual, arsitektur informasi, palet warna, tipografi, dan dial liveliness untuk Sistem Penilaian Sumatif TP Kurikulum Merdeka SMA.

---

## 1. Identitas & Karakter Produk

- **Nama Produk:** PLA Merdeka (Personalized Learning & Adaptive Assessment SMA)
- **Tujuan Sistem:** Membantu guru SMA mengelola penilaian sumatif berbasis Tujuan Pembelajaran (TP), menganalisis ketercapaian kompetensi, serta merumuskan rekomendasi intervensi diagnostik adaptif berdasarkan modalitas belajar siswa (VARK).
- **Karakter Visual:** 
  - *Serius, Presisi, dan Jelas:* Mengutamakan keterbacaan data numerik matriks nilai tanpa distraksi visual dekoratif berlebihan.
  - *Dukungan Guru yang Transparan:* Rekomendasi AI dihadirkan sebagai instrumen bantuan yang dapat ditinjau dan disesuaikan secara berdaulat oleh guru pengampu.

---

## 2. Dials (Tingkat Intensitas Desain)

Sesuai klasifikasi Liveliness Toolkit pada `antislop.md`:
- **ENERGY: 1 (Calm & Focused)**
  - UI tenang, didominasi latar Slate netral dan kartu putih bersih agar guru fokus pada input dan analisis nilai.
- **RHYTHM: 2 (Balanced)**
  - Variasi ritme berimbang antara kartu ringkasan eksekutif, lembar matriks spreadsheet, dan blok diagnostik intervensi.
- **MOTION: 1 (Subtle & Functional)**
  - Animasi dibatasi hanya pada transisi mikro interaktif (hover state, dialog modal, feedback submit form), menghormati preferensi `prefers-reduced-motion`.

---

## 3. Sistem Palet Warna & Rasional Keputusan (R-01, R-29, R-31)

Sistem warna dibatasi secara ketat pada 2 warna utama + warna status fungsional:

| Peran Warna | Token / Hex | Rasional Hierarki & Makna |
|---|---|---|
| **Primary Brand** | Teal (`#0d9488` / `teal-600` s/d `teal-800`) | Warna identitas akademik Kurikulum Merdeka, memberikan impresi ketenangan profesional dan kesegaran analitik. |
| **Neutral Surface** | Slate (`#f8fafc` s/d `#0f172a`) | Menjamin rasio kontras maksimal (WCAG AA > 4.5:1) untuk teks, angka NISN, dan batas tabel matriks. |
| **Status Ketuntasan (Tuntas)** | Emerald (`#059669` / `emerald-600`) | Indikator capaian TP yang memenuhi atau melampaui KKM. |
| **Status Ketuntasan (Remedial)** | Rose (`#e11d48` / `rose-600`) | Penanda afirmatif butir TP yang membutuhkan program remedial atau intervensi khusus. |
| **Kategori Gaya Belajar** | Purple (`#7c3aed` / `purple-600`) | Membedakan klasifikasi kualitatif modalitas belajar VARK dari metrik kuantitatif nilai. |
| **Modifikasi Guru** | Amber (`#d97706` / `amber-600`) | Penanda bahwa catatan diagnostik terbuka untuk diedit langsung oleh guru. |

---

## 4. Tipografi (R-06, R-31)

- **UI & Teks Umum:** `Plus Jakarta Sans`, sans-serif humanis modern yang dioptimalkan untuk keterbacaan antarmuka digital berbahasa Indonesia.
- **Data Numerik & Kode:** `JetBrains Mono`, monospace presisi untuk kode TP (`TP-01`), kode LM (`LM-01`), NISN siswa, dan skor nilai sumatif agar kolom angka sejajar rapi.

---

## 5. Standar Komponen & Aksesibilitas (R-08, R-39)

- Seluruh elemen interaktif (`button`, `a[href]`, `select`) wajib menampilkan `cursor: pointer` secara global.
- Tombol aksi tidak menggunakan panah dekoratif tanpa tujuan navigasi nyata.
- Seluruh ikon SVG wajib memiliki atribut `aria-hidden="true"`, dan tombol ikon wajib memiliki `aria-label` penjelas.
- Form submit asinkron atau proses analisis wajib menampilkan indikator loading state yang jelas.
