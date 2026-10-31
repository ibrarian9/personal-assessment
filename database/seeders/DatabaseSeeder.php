<?php

namespace Database\Seeders;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use App\Services\PersonalizationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Admin Guru
        User::updateOrCreate(
            ['email' => 'guru@sman1merdeka.sch.id'],
            [
                'name' => 'Dra. Sri Wahyuni, M.Pd.',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Settings Sekolah Kurikulum Merdeka
        Setting::set('nama_institusi', 'SMA Negeri 1 Merdeka', 'Nama Sekolah');
        Setting::set('tahun_akademik', '2025/2026 Ganjil', 'Tahun Ajaran & Semester');
        Setting::set('kkm_default', '75', 'Kriteria Ketuntasan Minimal / Ketercapaian TP');
        Setting::set('kurikulum', 'Kurikulum Merdeka (Fase E & F)', 'Kurikulum');
        Setting::set('kepala_sekolah', 'Dr. H. Bambang Sutrisno, M.Pd.', 'Kepala Sekolah');
        if (!Setting::get('ai_provider')) {
            Setting::set('ai_provider', config('services.ai.provider', 'router9'), 'AI Provider');
        }
        if (config('services.router9.api_key')) {
            Setting::set('router9_api_key', config('services.router9.api_key'), 'Router9 API Key');
        }
        if (config('services.router9.model')) {
            Setting::set('router9_model', config('services.router9.model'), 'Router9 Model');
        }
        if (config('services.router9.base_url')) {
            Setting::set('router9_base_url', config('services.router9.base_url'), 'Router9 Base URL');
        }
        if (config('services.openai.api_key')) {
            Setting::set('openai_api_key', config('services.openai.api_key'), 'OpenAI API Key');
        }
        if (config('services.gemini.api_key')) {
            Setting::set('gemini_api_key', config('services.gemini.api_key'), 'Gemini API Key');
        }

        // 3. Data Siswa SMA (Kelas X-A)
        $siswaData = [
            [
                'nisn' => '3144333149',
                'nama' => 'Aafif',
                'kelas' => 'X-A',
                'email' => 'aafif@sman1merdeka.sch.id',
                'gaya_belajar' => 'Visual',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3144384515',
                'nama' => 'Adakitya',
                'kelas' => 'X-A',
                'email' => 'adakitya@sman1merdeka.sch.id',
                'gaya_belajar' => 'Kinestetik',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3149876351',
                'nama' => 'Almira',
                'kelas' => 'X-A',
                'email' => 'almira@sman1merdeka.sch.id',
                'gaya_belajar' => 'Auditori',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3144738548',
                'nama' => 'Alif',
                'kelas' => 'X-A',
                'email' => 'alif@sman1merdeka.sch.id',
                'gaya_belajar' => 'Baca/Tulis',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3148538210',
                'nama' => 'Anjali',
                'kelas' => 'X-A',
                'email' => 'anjali@sman1merdeka.sch.id',
                'gaya_belajar' => 'Visual',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3149823411',
                'nama' => 'Budi Pratama',
                'kelas' => 'X-A',
                'email' => 'budi.pratama@sman1merdeka.sch.id',
                'gaya_belajar' => 'Kinestetik',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3148723901',
                'nama' => 'Citra Dewi Lestari',
                'kelas' => 'X-A',
                'email' => 'citra.dewi@sman1merdeka.sch.id',
                'gaya_belajar' => 'Auditori',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3145612349',
                'nama' => 'Dimas Anggara',
                'kelas' => 'X-A',
                'email' => 'dimas.anggara@sman1merdeka.sch.id',
                'gaya_belajar' => 'Visual',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3143901284',
                'nama' => 'Farah Nabila Zahra',
                'kelas' => 'X-A',
                'email' => 'farah.nabila@sman1merdeka.sch.id',
                'gaya_belajar' => 'Baca/Tulis',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3147819203',
                'nama' => 'Gilang Ramadhan',
                'kelas' => 'X-A',
                'email' => 'gilang.ramadhan@sman1merdeka.sch.id',
                'gaya_belajar' => 'Kinestetik',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3146723819',
                'nama' => 'Indah Permata Sari',
                'kelas' => 'X-A',
                'email' => 'indah.permata@sman1merdeka.sch.id',
                'gaya_belajar' => 'Auditori',
                'status' => 'Aktif',
            ],
            [
                'nisn' => '3148912304',
                'nama' => 'Kevin Sanjaya',
                'kelas' => 'X-A',
                'email' => 'kevin.sanjaya@sman1merdeka.sch.id',
                'gaya_belajar' => 'Visual',
                'status' => 'Aktif',
            ],
        ];

        $createdSiswas = [];
        foreach ($siswaData as $item) {
            $createdSiswas[] = Siswa::updateOrCreate(['nisn' => $item['nisn']], $item);
        }

        // 4. Mata Pelajaran SMA
        $mapelIpas = MataPelajaran::updateOrCreate(
            ['kode_mapel' => 'IPAS-X'],
            [
                'nama_mapel' => 'IPAS (Biologi & Sains Terapan)',
                'fase' => 'Fase E',
                'tingkat' => 'Kelas X',
                'kkm' => 75.00,
                'deskripsi' => 'Ilmu Pengetahuan Alam dan Sosial SMA Kurikulum Merdeka mencakup pemahaman sistem biologis manusia dan lingkungan.',
            ]
        );

        $mapelInformatika = MataPelajaran::updateOrCreate(
            ['kode_mapel' => 'INF-X'],
            [
                'nama_mapel' => 'Informatika',
                'fase' => 'Fase E',
                'tingkat' => 'Kelas X',
                'kkm' => 75.00,
                'deskripsi' => 'Berpikir komputasional, algoritma pemrograman Python, dan analisis data.',
            ]
        );

        $mapelMatematika = MataPelajaran::updateOrCreate(
            ['kode_mapel' => 'MAT-X'],
            [
                'nama_mapel' => 'Matematika',
                'fase' => 'Fase E',
                'tingkat' => 'Kelas X',
                'kkm' => 75.00,
                'deskripsi' => 'Eksponen, logaritma, barisan & deret, dan vektor spasial.',
            ]
        );

        // 5. Lingkup Materi & Tujuan Pembelajaran (TP) untuk IPAS (Sesuai gambar user)
        $materiData = [
            [
                'kode_lm' => 'Materi 1',
                'judul_materi' => 'Sistem Gerak dan Syaraf Manusia',
                'urutan' => 1,
                'tps' => [
                    [
                        'kode_tp' => 'TP 1.1',
                        'deskripsi_tp' => 'Mengidentifikasi organ utama penyusun sistem gerak manusia (tulang, sendi, otot, syaraf) melalui pengamatan torso, gambar, atau video tentang aktivitas dan sistem syaraf.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                    [
                        'kode_tp' => 'TP 1.2',
                        'deskripsi_tp' => 'Menganalisis fungsi masing-masing komponen sistem gerak dan syaraf.',
                        'target' => 75.00,
                        'urutan' => 2,
                    ],
                    [
                        'kode_tp' => 'TP 1.3',
                        'deskripsi_tp' => 'Menghubungkan antara jenis makanan yang dikonsumsi (misalnya kalsium, protein) dan kebiasaan beraktivitas dengan frekuensi olahraga, pola makan dan mengidentifikasi potensi risiko yang dapat menyebabkan gangguan pada sistem gerak.',
                        'target' => 75.00,
                        'urutan' => 3,
                    ],
                    [
                        'kode_tp' => 'TP 1.4',
                        'deskripsi_tp' => 'Menjaga kesehatan sistem organ gerak dengan membuat komitmen pribadi untuk menerapkan gaya hidup sehat dan poster infografis panduan menjaga tulang dan otot kuat.',
                        'target' => 75.00,
                        'urutan' => 4,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 2',
                'judul_materi' => 'Sistem Reproduksi & Pubertas',
                'urutan' => 2,
                'tps' => [
                    [
                        'kode_tp' => 'TP 2.1',
                        'deskripsi_tp' => 'Menganalisis perubahan fisik primer dan sekunder yang dialami laki-laki (misalnya mimpi basah) dan perempuan (misalnya menstruasi) selama masa pubertas.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                    [
                        'kode_tp' => 'TP 2.2',
                        'deskripsi_tp' => 'Menjelaskan cara menjaga kebersihan organ reproduksi dengan benar dan nutrisi yang penting untuk mendukung kesehatan selama masa pubertas.',
                        'target' => 75.00,
                        'urutan' => 2,
                    ],
                    [
                        'kode_tp' => 'TP 2.3',
                        'deskripsi_tp' => 'Mengklasifikasikan mitos yang tidak perlu dipercaya dan fakta ilmiah yang perlu dipahami terkait pubertas dan kesehatan reproduksi.',
                        'target' => 75.00,
                        'urutan' => 3,
                    ],
                    [
                        'kode_tp' => 'TP 2.4',
                        'deskripsi_tp' => 'Mengembangkan sikap positif dan penerimaan diri mengenai perubahan masa pubertas dengan merancang buklet informasi atau komik edukasi.',
                        'target' => 75.00,
                        'urutan' => 4,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 3',
                'judul_materi' => 'Sistem Peredaran Darah Manusia',
                'urutan' => 3,
                'tps' => [
                    [
                        'kode_tp' => 'TP 3.1',
                        'deskripsi_tp' => 'Mengidentifikasi organ peredaran darah manusia (jantung, pembuluh darah, dan darah) serta fungsinya masing-masing melalui pengamatan model torso dan video.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                    [
                        'kode_tp' => 'TP 3.2',
                        'deskripsi_tp' => 'Menjelaskan gangguan pada sistem peredaran darah, seperti anemia, hipertensi, dan penyakit jantung, serta cara pencegahannya.',
                        'target' => 75.00,
                        'urutan' => 2,
                    ],
                    [
                        'kode_tp' => 'TP 3.3',
                        'deskripsi_tp' => 'Mengukur frekuensi denyut nadi sendiri saat istirahat (sebelum aktivitas) dan setelah melakukan aktivitas fisik ringan.',
                        'target' => 75.00,
                        'urutan' => 3,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 4',
                'judul_materi' => 'Ekosistem & Keanekaragaman Hayati',
                'urutan' => 4,
                'tps' => [
                    [
                        'kode_tp' => 'TP 4.1',
                        'deskripsi_tp' => 'Menganalisis interaksi antar komponen biotik dan abiotik dalam suatu ekosistem.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                    [
                        'kode_tp' => 'TP 4.2',
                        'deskripsi_tp' => 'Menyajikan solusi atas ancaman kepunahan keanekaragaman hayati lokal di lingkungan sekitar.',
                        'target' => 75.00,
                        'urutan' => 2,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 5',
                'judul_materi' => 'Struktur Sel & Mikroskopik Organisme',
                'urutan' => 5,
                'tps' => [
                    [
                        'kode_tp' => 'TP 5.1',
                        'deskripsi_tp' => 'Mengamati struktur sel tumbuhan dan sel hewan menggunakan mikroskop optik / simulasi digital.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                    [
                        'kode_tp' => 'TP 5.2',
                        'deskripsi_tp' => 'Membedakan fungsi organel-organel utama sel (nukleus, mitokondria, kloroplas).',
                        'target' => 75.00,
                        'urutan' => 2,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 6',
                'judul_materi' => 'Metabolisme & Enzim',
                'urutan' => 6,
                'tps' => [
                    [
                        'kode_tp' => 'TP 6.1',
                        'deskripsi_tp' => 'Menjelaskan peran enzim sebagai biokatalisator dalam reaksi metabolisme tubuh.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 7',
                'judul_materi' => 'Genetika & Pewarisan Sifat',
                'urutan' => 7,
                'tps' => [
                    [
                        'kode_tp' => 'TP 7.1',
                        'deskripsi_tp' => 'Menganalisis hukum pewarisan sifat Mendel pada persilangan monohibrid dan dihibrid.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 8',
                'judul_materi' => 'Bioteknologi Konvensional & Modern',
                'urutan' => 8,
                'tps' => [
                    [
                        'kode_tp' => 'TP 8.1',
                        'deskripsi_tp' => 'Menerapkan prinsip bioteknologi konvensional dalam fermentasi makanan tradisional.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 9',
                'judul_materi' => 'Perubahan Lingkungan & Pemanasan Global',
                'urutan' => 9,
                'tps' => [
                    [
                        'kode_tp' => 'TP 9.1',
                        'deskripsi_tp' => 'Menganalisis data peningkatan suhu bumi dan emisi gas rumah kaca serta merancang aksi konservasi.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                ]
            ],
            [
                'kode_lm' => 'Materi 10',
                'judul_materi' => 'Zat Adiktif & Kesehatan Saraf',
                'urutan' => 10,
                'tps' => [
                    [
                        'kode_tp' => 'TP 10.1',
                        'deskripsi_tp' => 'Mengidentifikasi bahaya zat adiktif bagi sistem saraf dan merancang kampanye anti-narkoba di kalangan remaja.',
                        'target' => 75.00,
                        'urutan' => 1,
                    ],
                ]
            ],
        ];

        $allCreatedTps = [];
        foreach ($materiData as $mData) {
            $lm = LingkupMateri::updateOrCreate(
                [
                    'mata_pelajaran_id' => $mapelIpas->id,
                    'kode_lm' => $mData['kode_lm'],
                ],
                [
                    'judul_materi' => $mData['judul_materi'],
                    'urutan' => $mData['urutan'],
                ]
            );

            foreach ($mData['tps'] as $tpData) {
                $allCreatedTps[] = TujuanPembelajaran::updateOrCreate(
                    [
                        'lingkup_materi_id' => $lm->id,
                        'kode_tp' => $tpData['kode_tp'],
                    ],
                    [
                        'deskripsi_tp' => $tpData['deskripsi_tp'],
                        'target_ketercapaian' => $tpData['target'],
                        'urutan' => $tpData['urutan'],
                    ]
                );
            }
        }

        // 6. Matriks Penilaian Sumatif per-TP (Nilai seperti Gambar 1: 98, 99, 90, 99, 90, 99, 90, 87, 78, dst)
        $scoreMatrix = [
            // Aafif
            0 => [98, 99, 90, 99, 90, 99, 90, 87, 78, 92, 88, 95, 90, 88, 94, 90, 88, 92, 90, 95],
            // Adakitya
            1 => [98, 99, 90, 99, 90, 99, 90, 87, 78, 85, 82, 88, 80, 85, 78, 80, 85, 80, 82, 85],
            // Almira
            2 => [98, 99, 90, 99, 90, 99, 90, 87, 78, 90, 92, 94, 96, 90, 92, 90, 95, 92, 90, 94],
            // Alif
            3 => [98, 99, 90, 99, 90, 99, 90, 87, 78, 76, 80, 75, 78, 82, 80, 75, 78, 80, 78, 80],
            // Anjali
            4 => [98, 99, 90, 99, 90, 99, 90, 87, 78, 94, 96, 92, 90, 92, 95, 90, 92, 94, 90, 96],
            // Budi Pratama
            5 => [85, 88, 76, 80, 82, 85, 78, 80, 82, 85, 80, 84, 82, 85, 80, 78, 82, 85, 80, 84],
            // Citra Dewi (Sedang - beberapa TP remedial)
            6 => [75, 72, 70, 78, 74, 80, 72, 68, 70, 75, 74, 78, 72, 76, 74, 70, 72, 76, 74, 75],
            // Dimas Anggara (Perlu Pendampingan)
            7 => [60, 58, 62, 55, 65, 58, 60, 54, 58, 62, 55, 60, 58, 64, 58, 55, 60, 58, 62, 58],
            // Farah Nabila (Tinggi)
            8 => [95, 92, 90, 94, 98, 92, 95, 90, 94, 96, 92, 95, 90, 94, 98, 92, 95, 90, 94, 96],
            // Gilang Ramadhan (Sedang)
            9 => [78, 74, 72, 75, 80, 76, 78, 74, 75, 80, 76, 78, 74, 75, 80, 76, 78, 74, 75, 80],
            // Indah Permata (Perlu Pendampingan)
            10 => [58, 52, 55, 50, 60, 56, 58, 52, 55, 60, 56, 58, 52, 55, 60, 56, 58, 52, 55, 60],
            // Kevin Sanjaya (Tinggi)
            11 => [88, 85, 86, 90, 90, 88, 85, 86, 90, 90, 88, 85, 86, 90, 90, 88, 85, 86, 90, 90],
        ];

        foreach ($createdSiswas as $sIndex => $siswa) {
            $scores = $scoreMatrix[$sIndex] ?? array_fill(0, count($allCreatedTps), 80);

            foreach ($allCreatedTps as $tpIndex => $tp) {
                $nilai = (float) ($scores[$tpIndex] ?? 80.00);
                $status = NilaiSumatifTp::tentukanKetuntasan($nilai, $tp->target_ketercapaian);

                NilaiSumatifTp::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tujuan_pembelajaran_id' => $tp->id,
                    ],
                    [
                        'nilai' => $nilai,
                        'status_ketuntasan' => $status,
                        'catatan' => $status === 'Tuntas' ? 'Telah mencapai target TP' : 'Perlu bimbingan remedial',
                    ]
                );
            }
        }

        // 7. Generate Personalisasi awal untuk seluruh siswa secara cepat tanpa membakar kuota token API saat seeding
        $aiService = new \App\Services\AiPersonalizationService();
        foreach ($createdSiswas as $siswa) {
            $siswa->loadMissing('nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran');
            $aiService->fallbackGenerate($siswa, $siswa->nilaiSumatifTps, 'Inisialisasi Data Awal Sistem (Siap dianalisis ulang dengan AI)');
        }
    }
}
