# Dokumen Spesifikasi Activity Diagram
## Sistem Asesmen Personalisasi Siswa SMA (Kurikulum Merdeka & AI Engine)

Dokumen ini memuat kumpulan **Activity Diagram** komprehensif dalam format Mermaid yang memodelkan alur kerja operasional, logika komputasi ketuntasan TP, integrasi AI Engine, dan siklus pelaporan sistem.

---

## Daftar Isi Activity Diagram
1. [AD-01: Alur Penilaian Matriks Sumatif per-TP (Spreadsheet Grid Entry)](#ad-01-alur-penilaian-matriks-sumatif-per-tp)
2. [AD-02: Alur Analisis Diagnostik & Personalisasi Pembelajaran AI](#ad-02-alur-analisis-diagnostik--personalisasi-pembelajaran-ai)
3. [AD-03: Alur Manajemen Lingkup Materi & Tujuan Pembelajaran (TP)](#ad-03-alur-manajemen-lingkup-materi--tujuan-pembelajaran)
4. [AD-04: Alur Rekapitulasi & Pencetakan Raport Kurikulum Merdeka](#ad-04-alur-rekapitulasi--pencetakan-raport-kurikulum-merdeka)
5. [AD-05: Alur Konfigurasi Sekolah & Manajemen Provider AI](#ad-05-alur-konfigurasi-sekolah--manajemen-provider-ai)

---

## AD-01: Alur Penilaian Matriks Sumatif per-TP

Activity diagram ini memodelkan proses input nilai massal menggunakan antarmuka tabel spreadsheet *grid*, validasi batas angka (0 - 100), penyimpanan *batch*, serta kalkulasi otomatis ketuntasan TP dan predikat akhir.

```mermaid
flowchart TD
    Start(["Mulai"]) --> OpenMatrix["Guru Mengakses Menu 'Matriks Nilai per-TP'"]
    OpenMatrix --> SelectFilter["Pilih Mata Pelajaran & Kelas SMA"]
    SelectFilter --> LoadGrid["Sistem Merender Tabel Grid Spreadsheet:
    - Kolom Sticky: No, Nama Siswa, NISN
    - Kolom Dinamis: TP 1.1 s.d. TP 10.2
    - Kolom Kalkulasi: Rata-rata & Status"]
    
    LoadGrid --> EditScores["Guru Menginput / Memperbarui Skor per-TP pada Input Box"]
    EditScores --> ClickSave["Guru Menekan Tombol 'Simpan Seluruh Matriks Nilai'"]
    
    ClickSave --> ClientCheck{"Validasi Sisi Klien:
    Apakah Semua Nilai 0 - 100?"}
    
    ClientCheck -- Tidak (Invalid) --> ShowWarning["Tampilkan Peringatan Nilai Di Luar Rentang (0-100)"]
    ShowWarning --> EditScores
    
    ClientCheck -- Ya (Valid) --> SendPost["Kirim Data Array Nilai (siswa_id, tp_id, nilai) ke Server"]
    SendPost --> BeginTransaction["Server Membuka Database Transaction"]
    
    BeginTransaction --> LoopRows["Iterasi Setiap Pasangan Nilai Siswa & TP"]
    LoopRows --> EvalKKM{"Evaluasi Nilai terhadap KKM (>= 75.00)?"}
    
    EvalKKM -- Ya (Skor >= 75) --> SetTuntas["Set status_ketuntasan = 'Tuntas'"]
    EvalKKM -- Tidak (Skor < 75) --> SetBelumTuntas["Set status_ketuntasan = 'Belum Tuntas'"]
    
    SetTuntas --> SaveRow["Upsert Record ke Tabel 'nilai_sumatif_tps'"]
    SetBelumTuntas --> SaveRow
    
    SaveRow --> CheckMore{"Masih Ada Data Nilai Lain?"}
    CheckMore -- Ya --> LoopRows
    
    CheckMore -- Selesai --> CalcSummary["Hitung Ulang Statistik Siswa:
    - Rata-Rata Nilai Sumatif
    - Jumlah TP Tuntas & TP Belum Tuntas
    - Predikat Capaian (A / B / C / D)"]
    
    CalcSummary --> CommitDB["Commit Database Transaction"]
    CommitDB --> FlashSuccess["Kirim Notifikasi Flash: 'Matriks Nilai Berhasil Disimpan'"]
    FlashSuccess --> ReloadMatrix["Muat Ulang Halaman Matriks dengan Nilai Terkini"]
    ReloadMatrix --> End(["Selesai"])
```

---

## AD-02: Alur Analisis Diagnostik & Personalisasi Pembelajaran AI

Activity diagram ini memodelkan bagaimana sistem mengolah data ketuntasan TP, performa agregat, dan gaya belajar VARK melalui model bahasa kecerdasan buatan (Gemini / OpenAI API) atau *smart heuristic fallback* secara otomatis.

```mermaid
flowchart TD
    Start(["Mulai"]) --> Trigger["Pemicu: Guru Membuka Detail Personalisasi Siswa / Klik 'Refresh Analisis AI'"]
    Trigger --> FetchStudent["Sistem Mengambil Data Siswa Terkait:
    - Profil Siswa (Nama, NISN, Gaya Belajar VARK)
    - Daftar Lengkap Butir TP Belum Tuntas (Remedial)
    - Daftar Butir TP Tuntas (Pengayaan)
    - Rata-Rata Nilai Sumatif & Mata Pelajaran Terkait"]
    
    FetchStudent --> CheckProvider{"Periksa Konfigurasi AI di Tabel Settings:
    Apakah Provider = 'gemini' / 'openai' dan API Key Terisi?"}
    
    %% Alur AI LLM (Online)
    CheckProvider -- Ya (API Key Ada) --> ComposePrompt["Susun System & User Prompt Pedagogis Kurikulum Merdeka:
    - Sertakan Daftar TP Belum Tuntas
    - Sertakan Gaya Belajar Siswa
    - Instruksikan Output JSON Terstruktur"]
    
    ComposePrompt --> SendAPIRequest["Kirim HTTP POST Request ke AI Endpoint (Gemini / OpenAI)"]
    SendAPIRequest --> AwaitResponse{"Apakah Respon HTTP 200 & Format JSON Valid?"}
    
    AwaitResponse -- Ya (Sukses) --> ParseJSON["Ekstraksi Data JSON dari LLM:
    - Kategori Pemahaman ('Tinggi' / 'Sedang' / 'Perlu Pendampingan')
    - Rekomendasi Modalitas Belajar
    - Analisis Diagnostik & Kendala
    - Rekomendasi Materi & Butir Remedial
    - Strategi Aktivitas & Pembelajaran Adaptif"]
    
    %% Alur Heuristik (Offline / Fallback)
    AwaitResponse -- Tidak / Error / Timeout --> LogError["Catat Log Peringatan API Error"]
    LogError --> RunFallback["Jalankan Smart Heuristic Engine (Offline Rule-Based)"]
    CheckProvider -- Tidak (Tanpa API Key) --> RunFallback
    
    RunFallback --> DeterministicCalc["Hitung Kategori berdasarkan Persentase Ketuntasan TP:
    - 100% Tuntas -> 'Tinggi'
    - Sebagian Belum Tuntas -> 'Sedang'
    - Mayoritas Belum Tuntas -> 'Perlu Pendampingan'"]
    
    DeterministicCalc --> GenerateRuleStrategy["Susun Rekomendasi Remedial & Strategi Diferensiasi berdasarkan Matriks VARK"]
    
    %% Tahap Penyimpanan & Presentasi
    ParseJSON --> SaveDB["Simpan / Perbarui Record di Tabel 'personalisasis':
    - siswa_id, skor_performa, kategori_pemahaman
    - analisis_kekuatan, analisis_kelemahan
    - rekomendasi_materi, rekomendasi_aktivitas"]
    GenerateRuleStrategy --> SaveDB
    
    SaveDB --> RenderView["Tampilkan Kartu Profil Diagnostik AI & Strategi Adaptif kepada Guru"]
    RenderView --> End(["Selesai"])
```

---

## AD-03: Alur Manajemen Lingkup Materi & Tujuan Pembelajaran

Activity diagram ini menggambarkan proses pendefinisian hirarki kurikulum dari mata pelajaran ke Lingkup Materi (Materi 1..10) hingga butir-butir indikator Tujuan Pembelajaran (TP).

```mermaid
flowchart TD
    Start(["Mulai"]) --> OpenTPMenu["Guru Membuka Menu 'Lingkup Materi & TP'"]
    OpenTPMenu --> SelectSubject["Pilih Mata Pelajaran dari Dropdown Filter"]
    SelectSubject --> DisplayTree["Sistem Menampilkan Struktur Materi & TP Mata Pelajaran"]
    
    DisplayTree --> ChooseAction{"Pilih Aksi:"}
    
    %% Cabang 1: Tambah Lingkup Materi
    ChooseAction -- Tambah Lingkup Materi --> InputLM["Isi Form: Kode LM (Materi 1..10), Judul Materi, Urutan"]
    InputLM --> SubmitLM["Klik 'Tambah Materi'"]
    SubmitLM --> ValidateLM{"Validasi Input LM"}
    ValidateLM -- Valid --> SaveLM["Simpan Record ke Tabel 'lingkup_materis'"]
    ValidateLM -- Invalid --> ShowLMError["Tampilkan Pesan Error"]
    ShowLMError --> InputLM
    
    %% Cabang 2: Tambah Butir TP
    ChooseAction -- Tambah Butir TP --> ClickAddTP["Klik Tombol 'Tambah TP Baru'"]
    ClickAddTP --> InputTPForm["Pilih Lingkup Materi, Isi Kode TP (TP 1.1), Deskripsi Indikator, Target KKM"]
    InputTPForm --> SubmitTP["Klik 'Simpan TP'"]
    SubmitTP --> ValidateTP{"Validasi Input TP"}
    ValidateTP -- Valid --> SaveTP["Simpan Record ke Tabel 'tujuan_pembelajarans'"]
    ValidateTP -- Invalid --> ShowTPError["Tampilkan Pesan Error"]
    ShowTPError --> InputTPForm
    
    %% Cabang 3: Hapus Materi / TP
    ChooseAction -- Hapus TP / Materi --> ConfirmDelete{"Konfirmasi Dialog: Yakin Ingin Menghapus?"}
    ConfirmDelete -- Ya --> DeleteRecord["Hapus Record dari Database (Cascade Nilai Terkait)"]
    ConfirmDelete -- Batal --> DisplayTree
    
    SaveLM --> RefreshTree["Muat Ulang Tampilan Struktur TP Terkini"]
    SaveTP --> RefreshTree
    DeleteRecord --> RefreshTree
    RefreshTree --> End(["Selesai"])
```

---

## AD-04: Alur Rekapitulasi & Pencetakan Raport Kurikulum Merdeka

Activity diagram ini menggambarkan proses pembuatan laporan kumulatif capaian siswa dan pencetakan dokumen fisik/PDF raport sumatif.

```mermaid
flowchart TD
    Start(["Mulai"]) --> AccessReport["Pengguna Membuka Menu 'Laporan & Raport'"]
    AccessReport --> SelectFilter["Pilih Mata Pelajaran & Kelas SMA"]
    SelectFilter --> ClickApply["Klik 'Terapkan Filter'"]
    
    ClickApply --> AggregateData["Sistem Mengagregasi Data dari Database:
    - Menghitung Total Siswa di Kelas
    - Menghitung Jumlah Butir TP Tuntas & Remedial
    - Menghitung Rata-Rata Nilai Kelas & Rentang Nilai (Min - Max)
    - Menghitung Rerata Akhir per Siswa & Konversi Predikat (A/B/C/D)"]
    
    AggregateData --> DisplayReportTable["Tampilkan Kartu Statistik Kelas & Tabel Rekapitulasi Siswa"]
    DisplayReportTable --> DecidePrint{"Apakah Pengguna Ingin Mencetak / Ekspor Raport?"}
    
    DecidePrint -- Tidak --> ViewOnly["Pengguna Meninjau Data di Layar"]
    ViewOnly --> End(["Selesai"])
    
    DecidePrint -- Ya --> ClickPrintBtn["Klik Tombol 'Cetak / Export Raport Sumatif'"]
    ClickPrintBtn --> OpenPrintRoute["Sistem Membuka Route '/laporan/cetak' (New Tab)"]
    
    OpenPrintRoute --> RenderPrintLayout["Sistem Merender Template Khusus Cetak:
    - Kop Resmi Sekolah (SMA Negeri)
    - Identitas Siswa, Mata Pelajaran, & Tahun Akademik
    - Tabel Rincian Nilai per-TP & Nilai Akhir
    - Deskripsi Capaian Pembelajaran Kurikulum Merdeka
    - Kolom Tanda Tangan Guru Mata Pelajaran & Kepala Sekolah"]
    
    RenderPrintLayout --> TriggerBrowserPrint["Jalankan Skrip 'window.print()' Otomatis"]
    TriggerBrowserPrint --> ShowPrintDialog["Browser Menampilkan Dialog Cetak (Print to PDF / Printer)"]
    ShowPrintDialog --> End(["Selesai"])
```

---

## AD-05: Alur Konfigurasi Sekolah & Manajemen Provider AI

Activity diagram ini memodelkan alur konfigurasi identitas sekolah, nilai standar KKM, serta pengaturan integrasi kecerdasan buatan (Gemini / OpenAI).

```mermaid
flowchart TD
    Start(["Mulai"]) --> OpenSetting["Pengguna Mengakses Menu 'Pengaturan Sekolah & AI'"]
    OpenSetting --> LoadCurrentSettings["Sistem Membaca Konfigurasi Terkini dari Tabel 'settings'"]
    LoadCurrentSettings --> DisplayForm["Tampilkan Formulir Konfigurasi:
    - Identitas Sekolah (Nama, NIP Kepala Sekolah, Tahun Ajaran)
    - KKM Default Sekolah (misal: 75.00)
    - Pilihan AI Provider (Gemini / OpenAI / Offline Heuristic)
    - Input API Key (Google Gemini & OpenAI)
    - Opsi Checkbox: 'Hitung Ulang Seluruh Ketuntasan & Personalisasi'"]
    
    DisplayForm --> EditConfig["Pengguna Memperbarui Parameter Sesuai Kebutuhan"]
    EditConfig --> SubmitConfig["Klik 'Simpan Pengaturan'"]
    
    SubmitConfig --> ValidateConfig{"Validasi Data Input"}
    ValidateConfig -- Invalid --> ShowFormError["Tampilkan Pesan Error Validasi"]
    ShowFormError --> EditConfig
    
    ValidateConfig -- Valid --> SaveSettings["Simpan Konfigurasi ke Tabel 'settings'"]
    
    SaveSettings --> CheckRecalculate{"Apakah Opsi 'Hitung Ulang' Dicentang?"}
    
    CheckRecalculate -- Ya --> RunBatchRecalc["Jalankan Rekalkulasi Massal:
    1. Evaluasi Ulang Status Ketuntasan Seluruh 'nilai_sumatif_tps' terhadap KKM Baru
    2. Jalankan AI Personalization Service untuk Seluruh Siswa di Database"]
    
    CheckRecalculate -- Tidak --> ShowSuccessFlash["Tampilkan Notifikasi 'Pengaturan Berhasil Disimpan'"]
    RunBatchRecalc --> ShowSuccessFlash
    
    ShowSuccessFlash --> ReloadSettings["Muat Ulang Halaman Pengaturan"]
    ReloadSettings --> End(["Selesai"])
```
