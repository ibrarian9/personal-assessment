/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: personal_assessment
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswas`
--

DROP TABLE IF EXISTS `mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `program_studi` varchar(100) NOT NULL DEFAULT 'Teknik Informatika',
  `semester` int(11) NOT NULL DEFAULT 1,
  `gaya_belajar` enum('Visual','Auditori','Kinestetik','Baca/Tulis') NOT NULL DEFAULT 'Visual',
  `status` enum('Aktif','Cuti','Lulus') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mahasiswas_nim_unique` (`nim`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswas`
--

LOCK TABLES `mahasiswas` WRITE;
/*!40000 ALTER TABLE `mahasiswas` DISABLE KEYS */;
INSERT INTO `mahasiswas` VALUES
(1,'2023001001','Aditya Pratama Putra','aditya.pratama@mhs.ac.id','Teknik Informatika',3,'Visual','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(2,'2023001002','Siti Nurhaliza','siti.nurhaliza@mhs.ac.id','Sistem Informasi',3,'Auditori','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(3,'2023001003','Rian Maulana Hidayat','rian.maulana@mhs.ac.id','Teknik Informatika',3,'Kinestetik','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(4,'2023001004','Dewi Anggraini Kusuma','dewi.anggraini@mhs.ac.id','Teknik Informatika',5,'Baca/Tulis','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(5,'2023001005','Budi Santoso Wibowo','budi.santoso@mhs.ac.id','Rekayasa Perangkat Lunak',5,'Kinestetik','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(6,'2023001006','Farah Nabila Zahra','farah.nabila@mhs.ac.id','Sistem Informasi',3,'Visual','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(7,'2023001007','Gilang Ramadhan','gilang.ramadhan@mhs.ac.id','Teknik Informatika',7,'Kinestetik','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(8,'2023001008','Indah Permatasari','indah.permatasari@mhs.ac.id','Teknik Informatika',3,'Auditori','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(9,'2023001009','Kevin Sanjaya Pratama','kevin.sanjaya@mhs.ac.id','Teknik Informatika',5,'Visual','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(10,'2023001010','Lestari Ayu Wulandari','lestari.ayu@mhs.ac.id','Sistem Informasi',1,'Baca/Tulis','Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04');
/*!40000 ALTER TABLE `mahasiswas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mata_kuliahs`
--

DROP TABLE IF EXISTS `mata_kuliahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_kuliahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(20) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(11) NOT NULL DEFAULT 3,
  `semester` int(11) NOT NULL DEFAULT 1,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_kuliahs_kode_mk_unique` (`kode_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_kuliahs`
--

LOCK TABLES `mata_kuliahs` WRITE;
/*!40000 ALTER TABLE `mata_kuliahs` DISABLE KEYS */;
INSERT INTO `mata_kuliahs` VALUES
(1,'IF2101','Pemrograman Web Berbasis Komponen',3,3,'Pengembangan antarmuka web modern, state management, dan arsitektur komponen terdistribusi.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(2,'IF2102','Struktur Data dan Algoritma Lanjut',4,3,'Pohon biner, graf, teknik traversal, optimasi memori, dan analisis kompleksitas algoritma.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(3,'IF3103','Sistem Basis Data Terdistribusi',3,5,'Desain arsitektur basis data relasional & NoSQL berskala besar, sharding, dan replikasi data.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(4,'IF3104','Kecerdasan Buatan dan Machine Learning',3,5,'Algoritma machine learning terawasi & tak terawasi, neural network, dan analisis data cerdas.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(5,'IF4105','Rekayasa Perangkat Lunak Terapan',3,7,'Metodologi Agile/Scrum, CI/CD pipeline, automated testing, dan arsitektur microservices.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(6,'IF1106','Jaringan Komputer dan Keamanan Siber',3,1,'Protokol TCP/IP, routing & switching, firewall, enkripsi data, dan keamanan perimeter jaringan.','2026-08-21 20:31:04','2026-08-21 20:31:04');
/*!40000 ALTER TABLE `mata_kuliahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_01_01_000001_create_mahasiswas_table',1),
(5,'2026_01_01_000002_create_mata_kuliahs_table',1),
(6,'2026_01_01_000003_create_penilaians_table',1),
(7,'2026_01_01_000004_create_personalisasis_table',1),
(8,'2026_01_01_000005_create_settings_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penilaians`
--

DROP TABLE IF EXISTS `penilaians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penilaians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint(20) unsigned NOT NULL,
  `mata_kuliah_id` bigint(20) unsigned NOT NULL,
  `tugas` decimal(5,2) NOT NULL DEFAULT 0.00,
  `kuis` decimal(5,2) NOT NULL DEFAULT 0.00,
  `uts` decimal(5,2) NOT NULL DEFAULT 0.00,
  `uas` decimal(5,2) NOT NULL DEFAULT 0.00,
  `keaktifan` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_akhir` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grade` varchar(5) NOT NULL DEFAULT 'E',
  `status_kelulusan` enum('Lulus','Tidak Lulus') NOT NULL DEFAULT 'Lulus',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penilaians_mahasiswa_id_mata_kuliah_id_unique` (`mahasiswa_id`,`mata_kuliah_id`),
  KEY `penilaians_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  CONSTRAINT `penilaians_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penilaians_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penilaians`
--

LOCK TABLES `penilaians` WRITE;
/*!40000 ALTER TABLE `penilaians` DISABLE KEYS */;
INSERT INTO `penilaians` VALUES
(1,1,1,88.00,85.00,90.00,92.00,95.00,89.95,'A','Lulus','Aktif bertanya dan pengerjaan tugas sangat terstruktur.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(2,1,2,82.00,80.00,85.00,88.00,90.00,85.05,'A','Lulus','Implementasi algoritma berjalan lancar.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(3,2,1,78.00,70.00,75.00,76.00,80.00,75.65,'B+','Lulus','Perlu penguatan pada praktikum coding mandiri.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(4,2,2,72.00,65.00,70.00,68.00,75.00,69.55,'B-','Lulus','Tugas tepat waktu, namun kuis perlu ditingkatkan.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(5,3,1,60.00,50.00,55.00,52.00,65.00,55.35,'C','Lulus','Sering terkendala pada sintaks dasar web framework.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(6,3,2,55.00,45.00,50.00,48.00,60.00,50.65,'D','Tidak Lulus','Membutuhkan bimbingan remedial struktur data graf & tree.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(7,4,3,92.00,90.00,88.00,94.00,90.00,91.10,'A','Lulus','Analisis perancangan basis data terdistribusi sangat matang.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(8,4,4,90.00,88.00,92.00,90.00,95.00,90.70,'A','Lulus','Pemahaman model ML sangat mendalam.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(9,5,3,80.00,75.00,72.00,78.00,85.00,77.15,'B+','Lulus','Perlu eksplorasi optimasi sharding query.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(10,5,5,85.00,78.00,80.00,82.00,90.00,82.30,'A-','Lulus','Proyek Agile berjalan dengan baik.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(11,6,1,95.00,92.00,90.00,94.00,98.00,93.30,'A','Lulus','Desain UI dan integrasi komponen sangat rapi.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(12,7,5,75.00,70.00,78.00,74.00,80.00,75.20,'B+','Lulus','Konfigurasi CI/CD butuh sedikit penyempurnaan.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(13,8,1,58.00,52.00,55.00,50.00,60.00,54.15,'D','Tidak Lulus','Perlu pendampingan khusus pada logika routing & controller.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(14,9,4,76.00,74.00,72.00,75.00,80.00,74.80,'B','Lulus','Tugas klasifikasi ML selesai dengan hasil memuaskan.','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(15,10,6,88.00,85.00,86.00,90.00,90.00,87.85,'A','Lulus','Pemahaman konsep subnetting dan routing TCP/IP sangat kuat.','2026-08-21 20:31:04','2026-08-21 20:31:04');
/*!40000 ALTER TABLE `penilaians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personalisasis`
--

DROP TABLE IF EXISTS `personalisasis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personalisasis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint(20) unsigned NOT NULL,
  `mata_kuliah_id` bigint(20) unsigned DEFAULT NULL,
  `skor_performa` decimal(5,2) NOT NULL DEFAULT 0.00,
  `kategori_pemahaman` enum('Tinggi','Sedang','Perlu Pendampingan') NOT NULL DEFAULT 'Sedang',
  `gaya_belajar_rekomendasi` varchar(255) DEFAULT NULL,
  `rekomendasi_materi` text DEFAULT NULL,
  `rekomendasi_aktivitas` text DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personalisasis_mahasiswa_id_foreign` (`mahasiswa_id`),
  KEY `personalisasis_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  CONSTRAINT `personalisasis_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `personalisasis_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliahs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personalisasis`
--

LOCK TABLES `personalisasis` WRITE;
/*!40000 ALTER TABLE `personalisasis` DISABLE KEYS */;
INSERT INTO `personalisasis` VALUES
(1,1,NULL,87.50,'Tinggi','Visual','Modul diagram arsitektur tingkat lanjut, infografis ringkasan algoritma kompleks, dan studi perancangan sistem visual.','\n• Diberdayakan sebagai Peer Tutor / Asisten Belajar untuk memperdalam pemahaman konsep sekaligus memotivasi rekan sejawat.\n• Diberikan penugasan pengayaan khusus (Enrichment Challenge) berskala proyek terapan.','Performa akademik sangat memuaskan (Rata-rata: 87.5). Mahasiswa menunjukkan penguasaan materi yang kuat dan siap untuk materi akselerasi.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(2,2,NULL,72.60,'Sedang','Auditori','Audio rekaman penjelasan materi per bab, diskusi kelompok terbimbing, dan tanya-jawab lisan.','\n• Jadwalkan sesi belajar kelompok rutin 2x seminggu untuk membedah tugas dan materi yang masih diragukan.\n• Konsultasikan topik yang memiliki skor kuis rendah kepada dosen pengampu / asisten.','Performa akademik cukup baik (Rata-rata: 72.6). Perlu menjaga konsistensi pengerjaan tugas dan peningkatan pada komponen evaluasi utama.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(3,3,NULL,53.00,'Perlu Pendampingan','Kinestetik','Latihan coding dasar dengan template yang dipandu, latihan interaktif step-by-step, dan workshop pendampingan tutor.','\n• Lakukan simulasi ujian mandiri dengan batasan waktu dan tinjau kembali pembahasan soal UTS/UAS sebelumnya.\n• Wajib mengikuti sesi bimbingan remedial mingguan dan bimbingan akademik intensif.\n• Fokus pada penguatan konsep fundamental materi dasar sebelum melangkah ke topik lanjutan.','Performa akademik memerlukan perhatian khusus (Rata-rata: 53). Disarankan segera melakukan remedial dan intervensi strategi belajar adaptif.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(4,4,NULL,90.90,'Tinggi','Baca/Tulis','Dokumentasi teknis resmi, paper riset akademik terkini, dan pembuatan laporan analisis komparatif sistem.','\n• Diberdayakan sebagai Peer Tutor / Asisten Belajar untuk memperdalam pemahaman konsep sekaligus memotivasi rekan sejawat.\n• Diberikan penugasan pengayaan khusus (Enrichment Challenge) berskala proyek terapan.','Performa akademik sangat memuaskan (Rata-rata: 90.9). Mahasiswa menunjukkan penguasaan materi yang kuat dan siap untuk materi akselerasi.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(5,5,NULL,79.73,'Sedang','Kinestetik','Praktikum langsung di laboratorium, latihan live-coding terarah, dan latihan pemecahan studi kasus bertahap.','\n• Jadwalkan sesi belajar kelompok rutin 2x seminggu untuk membedah tugas dan materi yang masih diragukan.\n• Konsultasikan topik yang memiliki skor kuis rendah kepada dosen pengampu / asisten.','Performa akademik cukup baik (Rata-rata: 79.725). Perlu menjaga konsistensi pengerjaan tugas dan peningkatan pada komponen evaluasi utama.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(6,6,NULL,93.30,'Tinggi','Visual','Modul diagram arsitektur tingkat lanjut, infografis ringkasan algoritma kompleks, dan studi perancangan sistem visual.','\n• Diberdayakan sebagai Peer Tutor / Asisten Belajar untuk memperdalam pemahaman konsep sekaligus memotivasi rekan sejawat.\n• Diberikan penugasan pengayaan khusus (Enrichment Challenge) berskala proyek terapan.','Performa akademik sangat memuaskan (Rata-rata: 93.3). Mahasiswa menunjukkan penguasaan materi yang kuat dan siap untuk materi akselerasi.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(7,7,NULL,75.20,'Sedang','Kinestetik','Praktikum langsung di laboratorium, latihan live-coding terarah, dan latihan pemecahan studi kasus bertahap.','\n• Jadwalkan sesi belajar kelompok rutin 2x seminggu untuk membedah tugas dan materi yang masih diragukan.\n• Konsultasikan topik yang memiliki skor kuis rendah kepada dosen pengampu / asisten.','Performa akademik cukup baik (Rata-rata: 75.2). Perlu menjaga konsistensi pengerjaan tugas dan peningkatan pada komponen evaluasi utama.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(8,8,NULL,54.15,'Perlu Pendampingan','Auditori','Penjelasan audio singkat per konsep dasar, sesi konsultasi privat dengan tutor, dan re-listening materi kuliah.','\n• Lakukan simulasi ujian mandiri dengan batasan waktu dan tinjau kembali pembahasan soal UTS/UAS sebelumnya.\n• Wajib mengikuti sesi bimbingan remedial mingguan dan bimbingan akademik intensif.\n• Fokus pada penguatan konsep fundamental materi dasar sebelum melangkah ke topik lanjutan.','Performa akademik memerlukan perhatian khusus (Rata-rata: 54.15). Disarankan segera melakukan remedial dan intervensi strategi belajar adaptif.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(9,9,NULL,74.80,'Sedang','Visual','Slide presentasi interaktif dengan bagan alir terstruktur, video tutorial animasi, dan mind map konsep.','\n• Jadwalkan sesi belajar kelompok rutin 2x seminggu untuk membedah tugas dan materi yang masih diragukan.\n• Konsultasikan topik yang memiliki skor kuis rendah kepada dosen pengampu / asisten.','Performa akademik cukup baik (Rata-rata: 74.8). Perlu menjaga konsistensi pengerjaan tugas dan peningkatan pada komponen evaluasi utama.','2026-08-21 20:31:05','2026-08-21 20:31:05'),
(10,10,NULL,87.85,'Tinggi','Baca/Tulis','Dokumentasi teknis resmi, paper riset akademik terkini, dan pembuatan laporan analisis komparatif sistem.','\n• Diberdayakan sebagai Peer Tutor / Asisten Belajar untuk memperdalam pemahaman konsep sekaligus memotivasi rekan sejawat.\n• Diberikan penugasan pengayaan khusus (Enrichment Challenge) berskala proyek terapan.','Performa akademik sangat memuaskan (Rata-rata: 87.85). Mahasiswa menunjukkan penguasaan materi yang kuat dan siap untuk materi akselerasi.','2026-08-21 20:31:05','2026-08-21 20:31:05');
/*!40000 ALTER TABLE `personalisasis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'bobot_tugas','20','Bobot Tugas / Proyek (20%)','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(2,'bobot_kuis','15','Bobot Kuis Berkala (15%)','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(3,'bobot_uts','25','Bobot Ujian Tengah Semester (25%)','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(4,'bobot_uas','30','Bobot Ujian Akhir Semester (30%)','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(5,'bobot_keaktifan','10','Bobot Keaktifan & Partisipasi (10%)','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(6,'nama_institusi','Institut Teknologi & Sains Digital','Nama Perguruan Tinggi','2026-08-21 20:31:04','2026-08-21 20:31:04'),
(7,'tahun_akademik','2025/2026 Ganjil','Semester Aktif','2026-08-21 20:31:04','2026-08-21 20:31:04');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-22 10:38:43
