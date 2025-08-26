/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80403 (8.4.3)
 Source Host           : localhost:3306
 Source Schema         : sirekap

 Target Server Type    : MySQL
 Target Server Version : 80403 (8.4.3)
 File Encoding         : 65001

 Date: 26/08/2025 09:30:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for guru_asuh
-- ----------------------------
DROP TABLE IF EXISTS `guru_asuh`;
CREATE TABLE `guru_asuh`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guru_id` bigint UNSIGNED NOT NULL,
  `siswa_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `guru_asuh_guru_id_foreign`(`guru_id` ASC) USING BTREE,
  INDEX `guru_asuh_siswa_id_foreign`(`siswa_id` ASC) USING BTREE,
  CONSTRAINT `guru_asuh_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `guru_asuh_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of guru_asuh
-- ----------------------------
INSERT INTO `guru_asuh` VALUES (2, 1, 2, '2025-08-21 07:45:01', '2025-08-21 07:45:01');
INSERT INTO `guru_asuh` VALUES (3, 1, 1, '2025-08-21 07:45:31', '2025-08-21 07:45:31');
INSERT INTO `guru_asuh` VALUES (4, 2, 3, '2025-08-25 06:36:33', '2025-08-25 06:36:33');

-- ----------------------------
-- Table structure for guru_mata_pelajaran
-- ----------------------------
DROP TABLE IF EXISTS `guru_mata_pelajaran`;
CREATE TABLE `guru_mata_pelajaran`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `guru_id` bigint UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `guru_mata_pelajaran_guru_id_foreign`(`guru_id` ASC) USING BTREE,
  INDEX `guru_mata_pelajaran_mata_pelajaran_id_foreign`(`mata_pelajaran_id` ASC) USING BTREE,
  INDEX `guru_mata_pelajaran_kelas_id_foreign`(`kelas_id` ASC) USING BTREE,
  CONSTRAINT `guru_mata_pelajaran_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `guru_mata_pelajaran_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `guru_mata_pelajaran_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of guru_mata_pelajaran
-- ----------------------------
INSERT INTO `guru_mata_pelajaran` VALUES (1, 1, 1, 1, '2025-08-20 12:06:47', '2025-08-20 12:06:47');
INSERT INTO `guru_mata_pelajaran` VALUES (2, 2, 2, 1, '2025-08-25 06:37:06', '2025-08-25 06:37:06');

-- ----------------------------
-- Table structure for gurus
-- ----------------------------
DROP TABLE IF EXISTS `gurus`;
CREATE TABLE `gurus`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `nip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `gurus_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `gurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gurus
-- ----------------------------
INSERT INTO `gurus` VALUES (1, 3, '-', 'ds. tirak kec. kwadungan', '085655600061', '2025-08-20 11:43:03', '2025-08-21 01:34:43', NULL);
INSERT INTO `gurus` VALUES (2, 6, NULL, 'kronggahan', '085655606767', '2025-08-23 03:53:59', '2025-08-23 03:53:59', NULL);
INSERT INTO `gurus` VALUES (3, 7, NULL, 'dungpring', '087978', '2025-08-23 03:55:26', '2025-08-23 03:55:26', NULL);

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for jurusans
-- ----------------------------
DROP TABLE IF EXISTS `jurusans`;
CREATE TABLE `jurusans`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_jurusan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `singkatan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jurusans
-- ----------------------------
INSERT INTO `jurusans` VALUES (1, 'Desain Komunikasi Visual', 'DKV', '2025-08-20 09:18:12', '2025-08-20 09:18:12');
INSERT INTO `jurusans` VALUES (2, 'Teknik Pemesinan', 'TPM', '2025-08-20 11:23:08', '2025-08-20 11:23:08');

-- ----------------------------
-- Table structure for kelas
-- ----------------------------
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `jurusan_id` bigint UNSIGNED NOT NULL,
  `tingkat` enum('10','11','12') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `kelas_jurusan_id_foreign`(`jurusan_id` ASC) USING BTREE,
  CONSTRAINT `kelas_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kelas
-- ----------------------------
INSERT INTO `kelas` VALUES (1, 1, '10', 'X DKV', '2025-08-20 11:23:26', '2025-08-20 11:23:26');
INSERT INTO `kelas` VALUES (2, 2, '10', 'X TPM', '2025-08-20 11:23:43', '2025-08-20 11:23:43');

-- ----------------------------
-- Table structure for mata_pelajarans
-- ----------------------------
DROP TABLE IF EXISTS `mata_pelajarans`;
CREATE TABLE `mata_pelajarans`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mata_pelajarans_kode_mapel_unique`(`kode_mapel` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mata_pelajarans
-- ----------------------------
INSERT INTO `mata_pelajarans` VALUES (1, 'MTK', 'Matematika', '2025-08-20 11:34:09', '2025-08-20 11:34:09');
INSERT INTO `mata_pelajarans` VALUES (2, 'PAI', 'Pendidikan Agama Islam', '2025-08-22 11:06:53', '2025-08-22 11:06:53');
INSERT INTO `mata_pelajarans` VALUES (3, 'PKN', 'PKN', '2025-08-23 06:38:35', '2025-08-23 06:38:35');
INSERT INTO `mata_pelajarans` VALUES (4, 'DSN', 'DESAIN', '2025-08-24 06:18:02', '2025-08-24 06:18:02');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2025_08_20_074517_add_role_and_photo_to_users_table', 1);
INSERT INTO `migrations` VALUES (5, '2025_08_20_074612_create_jurusans_table', 1);
INSERT INTO `migrations` VALUES (6, '2025_08_20_074639_create_kelas_table', 1);
INSERT INTO `migrations` VALUES (7, '2025_08_20_074659_create_mata_pelajarans_table', 1);
INSERT INTO `migrations` VALUES (8, '2025_08_20_074740_create_gurus_table', 1);
INSERT INTO `migrations` VALUES (9, '2025_08_20_082123_create_siswas_table', 1);
INSERT INTO `migrations` VALUES (10, '2025_08_20_082155_create_guru_mata_pelajaran_table', 1);
INSERT INTO `migrations` VALUES (11, '2025_08_20_082242_create_nilais_table', 1);
INSERT INTO `migrations` VALUES (12, '2025_08_20_082308_create_presensis_table', 1);
INSERT INTO `migrations` VALUES (13, '2025_08_20_130019_create_guru_asuh_table', 2);
INSERT INTO `migrations` VALUES (14, '2025_08_21_010604_add_tanggal_to_nilais_table', 3);
INSERT INTO `migrations` VALUES (15, '2025_08_21_043636_create_sekolah_table', 4);
INSERT INTO `migrations` VALUES (16, '2025_08_21_072215_recreate_guru_asuh_table', 5);
INSERT INTO `migrations` VALUES (17, '2025_08_22_113211_make_nip_nullable_in_gurus_table', 6);
INSERT INTO `migrations` VALUES (18, '2025_08_25_054202_add_pms_status_to_presensis_table', 7);

-- ----------------------------
-- Table structure for nilais
-- ----------------------------
DROP TABLE IF EXISTS `nilais`;
CREATE TABLE `nilais`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint UNSIGNED NOT NULL,
  `guru_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `jenis_nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Harian',
  `nilai_tugas` decimal(5, 0) NULL DEFAULT NULL,
  `nilai_uts` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_uas` decimal(5, 2) NULL DEFAULT NULL,
  `nilai_akhir` decimal(5, 2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nilais_siswa_id_mata_pelajaran_id_tanggal_jenis_nilai_unique`(`siswa_id` ASC, `mata_pelajaran_id` ASC, `tanggal` ASC, `jenis_nilai` ASC) USING BTREE,
  INDEX `nilais_guru_id_foreign`(`guru_id` ASC) USING BTREE,
  INDEX `nilais_mata_pelajaran_id_foreign`(`mata_pelajaran_id` ASC) USING BTREE,
  CONSTRAINT `nilais_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `nilais_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `nilais_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nilais
-- ----------------------------
INSERT INTO `nilais` VALUES (5, 1, 1, 1, '2025-08-21', 'Harian', 90, NULL, NULL, NULL, '2025-08-21 01:16:17', '2025-08-21 01:16:17');
INSERT INTO `nilais` VALUES (6, 2, 1, 1, '2025-08-21', 'Harian', 80, NULL, NULL, NULL, '2025-08-21 01:16:17', '2025-08-21 01:16:17');
INSERT INTO `nilais` VALUES (7, 1, 1, 1, '2025-08-20', 'Harian', 90, NULL, NULL, NULL, '2025-08-21 02:03:02', '2025-08-21 02:03:02');
INSERT INTO `nilais` VALUES (8, 2, 1, 1, '2025-08-20', 'Harian', 98, NULL, NULL, NULL, '2025-08-21 02:03:02', '2025-08-21 02:03:02');
INSERT INTO `nilais` VALUES (9, 1, 1, 1, '2025-08-23', 'Harian', 90, NULL, NULL, NULL, '2025-08-22 10:54:09', '2025-08-22 10:54:09');
INSERT INTO `nilais` VALUES (10, 2, 1, 1, '2025-08-23', 'Harian', 89, NULL, NULL, NULL, '2025-08-22 10:54:09', '2025-08-25 01:34:51');
INSERT INTO `nilais` VALUES (11, 3, 1, 1, '2025-08-23', 'Harian', 88, NULL, NULL, NULL, '2025-08-23 08:19:08', '2025-08-23 08:19:08');
INSERT INTO `nilais` VALUES (12, 3, 1, 1, '2025-08-21', 'Harian', 85, NULL, NULL, NULL, '2025-08-23 08:19:20', '2025-08-23 08:19:20');
INSERT INTO `nilais` VALUES (13, 1, 1, 1, '2025-08-18', 'Harian', 89, NULL, NULL, NULL, '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `nilais` VALUES (14, 2, 1, 1, '2025-08-18', 'Harian', 90, NULL, NULL, NULL, '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `nilais` VALUES (15, 3, 1, 1, '2025-08-18', 'Harian', 78, NULL, NULL, NULL, '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `nilais` VALUES (16, 1, 1, 1, '2025-08-19', 'Harian', 89, NULL, NULL, NULL, '2025-08-25 01:36:03', '2025-08-25 01:36:03');
INSERT INTO `nilais` VALUES (17, 2, 1, 1, '2025-08-19', 'Harian', 87, NULL, NULL, NULL, '2025-08-25 01:36:03', '2025-08-25 01:36:03');
INSERT INTO `nilais` VALUES (18, 3, 1, 1, '2025-08-19', 'Harian', 85, NULL, NULL, NULL, '2025-08-25 01:36:03', '2025-08-25 01:36:03');
INSERT INTO `nilais` VALUES (19, 1, 1, 1, '2025-08-25', 'Harian', 89, NULL, NULL, NULL, '2025-08-25 01:45:39', '2025-08-25 01:45:39');
INSERT INTO `nilais` VALUES (20, 2, 1, 1, '2025-08-25', 'Harian', 98, NULL, NULL, NULL, '2025-08-25 01:45:39', '2025-08-25 06:30:35');
INSERT INTO `nilais` VALUES (21, 3, 1, 1, '2025-08-25', 'Harian', 89, NULL, NULL, NULL, '2025-08-25 01:45:39', '2025-08-25 01:45:39');
INSERT INTO `nilais` VALUES (22, 2, 2, 2, '2025-08-25', 'Harian', 89, NULL, NULL, NULL, '2025-08-25 06:39:33', '2025-08-25 06:39:33');
INSERT INTO `nilais` VALUES (23, 3, 2, 2, '2025-08-25', 'Harian', 70, NULL, NULL, NULL, '2025-08-25 06:39:33', '2025-08-25 06:39:33');

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for presensis
-- ----------------------------
DROP TABLE IF EXISTS `presensis`;
CREATE TABLE `presensis`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint UNSIGNED NOT NULL,
  `guru_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa','PMS') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `presensis_siswa_id_mata_pelajaran_id_tanggal_unique`(`siswa_id` ASC, `mata_pelajaran_id` ASC, `tanggal` ASC) USING BTREE,
  INDEX `presensis_mata_pelajaran_id_foreign`(`mata_pelajaran_id` ASC) USING BTREE,
  INDEX `presensis_guru_id_foreign`(`guru_id` ASC) USING BTREE,
  CONSTRAINT `presensis_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `presensis_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `presensis_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of presensis
-- ----------------------------
INSERT INTO `presensis` VALUES (1, 1, 1, 1, '2025-08-21', 'Hadir', '2025-08-21 01:00:53', '2025-08-21 01:00:53');
INSERT INTO `presensis` VALUES (2, 2, 1, 1, '2025-08-21', 'Hadir', '2025-08-21 01:00:53', '2025-08-21 01:00:53');
INSERT INTO `presensis` VALUES (3, 1, 1, 1, '2025-08-19', 'Hadir', '2025-08-21 01:02:06', '2025-08-21 01:02:06');
INSERT INTO `presensis` VALUES (4, 2, 1, 1, '2025-08-19', 'Hadir', '2025-08-21 01:02:06', '2025-08-21 01:02:06');
INSERT INTO `presensis` VALUES (5, 1, 1, 1, '2025-08-20', 'Hadir', '2025-08-21 02:03:02', '2025-08-21 02:03:02');
INSERT INTO `presensis` VALUES (6, 2, 1, 1, '2025-08-20', 'Hadir', '2025-08-21 02:03:02', '2025-08-21 02:03:02');
INSERT INTO `presensis` VALUES (7, 1, 1, 1, '2025-08-23', 'Hadir', '2025-08-22 10:54:09', '2025-08-22 10:54:09');
INSERT INTO `presensis` VALUES (8, 2, 1, 1, '2025-08-23', 'Hadir', '2025-08-22 10:54:09', '2025-08-22 10:54:09');
INSERT INTO `presensis` VALUES (9, 3, 1, 1, '2025-08-23', 'Hadir', '2025-08-23 08:19:08', '2025-08-23 08:19:08');
INSERT INTO `presensis` VALUES (10, 3, 1, 1, '2025-08-21', 'Hadir', '2025-08-23 08:19:20', '2025-08-23 08:19:20');
INSERT INTO `presensis` VALUES (11, 1, 1, 1, '2025-08-18', 'Sakit', '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `presensis` VALUES (12, 2, 1, 1, '2025-08-18', 'Hadir', '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `presensis` VALUES (13, 3, 1, 1, '2025-08-18', 'Hadir', '2025-08-23 08:19:43', '2025-08-23 08:19:43');
INSERT INTO `presensis` VALUES (14, 3, 1, 1, '2025-08-19', 'Hadir', '2025-08-25 01:36:03', '2025-08-25 01:36:03');
INSERT INTO `presensis` VALUES (15, 1, 1, 1, '2025-08-25', 'Hadir', '2025-08-25 01:45:39', '2025-08-25 01:45:39');
INSERT INTO `presensis` VALUES (16, 2, 1, 1, '2025-08-25', 'Hadir', '2025-08-25 01:45:39', '2025-08-25 01:45:39');
INSERT INTO `presensis` VALUES (17, 3, 1, 1, '2025-08-25', 'Hadir', '2025-08-25 01:45:39', '2025-08-25 01:45:39');
INSERT INTO `presensis` VALUES (18, 1, 2, 2, '2025-08-25', 'Alpa', '2025-08-25 06:39:33', '2025-08-25 06:39:33');
INSERT INTO `presensis` VALUES (19, 2, 2, 2, '2025-08-25', 'Hadir', '2025-08-25 06:39:33', '2025-08-25 06:39:33');
INSERT INTO `presensis` VALUES (20, 3, 2, 2, '2025-08-25', 'Hadir', '2025-08-25 06:39:33', '2025-08-25 06:39:33');

-- ----------------------------
-- Table structure for sekolah
-- ----------------------------
DROP TABLE IF EXISTS `sekolah`;
CREATE TABLE `sekolah`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `npsn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sekolah
-- ----------------------------
INSERT INTO `sekolah` VALUES (1, 'SMK PGRI 1 MEJAYAN', '20507704', 'Jl. Kolonel Marhadi No.25 Mejayan', '(0351) 383276', 'smkpgri_mejayan@yahoo.com', 'logos/IkgbMC7XwRxJVb9qvVDpBpcPi2ND58jww4gU7iRp.png', 'logos/BPinfnELzvz5pgwUPDSPlVQ7R3yihFmg7B3PXY9p.png', '2025-08-21 04:45:47', '2025-08-21 05:19:18');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('5dvvK6tDpuz5QzzN0SNkQYTJw9WZZ1iRg43BCUsU', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUjF0aW50M1JFTXFtaDRLUUpyU0NZdDd4TUdFaWhGYUNyYWF1ZWF2diI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo5MDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3Jla2FwLXBlci1ndXJ1P2J1bGFuPTIwMjUtMDgmZ3VydV9pZD0xJmp1cnVzYW5faWQ9MSZtYXBlbF9pZD0xIjt9fQ==', 1756174956);
INSERT INTO `sessions` VALUES ('BdYv2kgmuCPZh8IwOkr1kPDlpvk5KT5YLGMtVmFX', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZUJGUTQ5czRJUFZLMFhYQzkyOVdnTmF5cUMxd3NEU0VBNmJxQ3pYciI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZ3VydS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1756103361);
INSERT INTO `sessions` VALUES ('cKZhHgFQOuMFbq7d1M6EbM11QQq21WQpuqvjjjEY', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMnBxUldpTHdpMk9BY0tGUHFWbk5Zc1djWDZEQURaZjdCWVRESGl6eiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2lzd2EvZGFzaGJvYXJkP2J1bGFuPTIwMjUtMDgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1756101091);
INSERT INTO `sessions` VALUES ('Fl6JxhSavRhXGU6T73Eka9eXk4cx2kso3F5hGKz5', 2, '10.110.2.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVnRRMzVjSG5JY0lhN1BFVUlSMVg3bld5NnZLQUE5ZGdrWEFWRUhvTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMC4xMTAuMi40NS9zaXJla2FwL3B1YmxpYy9zaXN3YS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1756102851);
INSERT INTO `sessions` VALUES ('qDZvAsymxbCOtgst9d7Dfg41TaYEeniV3ak3urgQ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUlhaTWhnT2kzQjVDOTd1dmxkSHhxRTBCUzJ6Vzl3azM3UkRGWnNuWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2Rhc2hib2FyZCI7fX0=', 1756101813);
INSERT INTO `sessions` VALUES ('scDoIWKBStIYgilHzO1Xba4zfHHPROdKpHbb6OUH', 6, '10.110.2.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidEl1a3NjZExYTnpYNm50OElTWTVvS2RtZmhvZ3YxTWRETnJwdkRXeSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA0OiJodHRwOi8vMTAuMTEwLjIuNDUvc2lyZWthcC9wdWJsaWMvZ3VydS9yZWthcC1hbmFrLWFzdWg/YnVsYW49MjAyNS0wOCZqdXJ1c2FuX2lkPTEma2VsYXNfaWQ9MSZzaXN3YV9uYW1hPSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==', 1756105515);
INSERT INTO `sessions` VALUES ('Y7Vvj5lwt3Cd5xsZyzqWjKkZbX1s9pjGaJZdc4lO', 1, '10.110.2.55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTDI0ZWtibnVNVzd5NmlFaU45Q0tIS3FGdmZWQlBjaGY5N3pPWGZMOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMC4xMTAuMi40NS9zaXJla2FwL3B1YmxpYy9hZG1pbi9tYXRhcGVsYWphcmFuIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1756107230);
INSERT INTO `sessions` VALUES ('zEk7gU0xcoxc0HIzMHBBY3ueDHwfXthJ4uwfqqIR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibkhuYlRaNDJtaVBzMU13cnpkdTdZcTkwSFdJUXlvQTJwNVA4aHg4YSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1756111187);

-- ----------------------------
-- Table structure for siswas
-- ----------------------------
DROP TABLE IF EXISTS `siswas`;
CREATE TABLE `siswas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `nis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `no_telepon_orang_tua` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `siswas_nis_unique`(`nis` ASC) USING BTREE,
  UNIQUE INDEX `siswas_nisn_unique`(`nisn` ASC) USING BTREE,
  INDEX `siswas_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `siswas_kelas_id_foreign`(`kelas_id` ASC) USING BTREE,
  CONSTRAINT `siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `siswas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of siswas
-- ----------------------------
INSERT INTO `siswas` VALUES (1, 2, 1, '12345', '12345', 'Ds. Kaliabu', '0865', '2025-08-20 11:24:58', '2025-08-20 11:24:58');
INSERT INTO `siswas` VALUES (2, 4, 1, '-', '-', 'Ds. kerten', '0865', '2025-08-20 12:30:20', '2025-08-20 12:30:20');
INSERT INTO `siswas` VALUES (3, 5, 1, '123134', '24341241', 'skdjkf', '0778987', '2025-08-22 11:10:50', '2025-08-22 11:10:50');
INSERT INTO `siswas` VALUES (4, 8, 2, '1212232', NULL, 'reregf', '08908', '2025-08-25 03:34:55', '2025-08-25 03:34:55');
INSERT INTO `siswas` VALUES (5, 9, 2, '12132323', NULL, 'lkgjkfdgjsdl', '8908968', '2025-08-25 03:36:35', '2025-08-25 03:36:35');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','guru','siswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin Aplikasi', 'admin@contoh.com', NULL, '$2y$12$Ti4ku6oWDd3pwseA0tQhGu11x4vfqJcHaPzc3uB/pD2r6vZDOXVju', NULL, '2025-08-20 10:17:13', '2025-08-20 10:17:13', 'admin', NULL);
INSERT INTO `users` VALUES (2, 'Indah Kurnia Abadi', 'indah@gmail.com', NULL, '$2y$12$f3U2drlG1HGdU..qMYVzRuQPdHEgxnM2lKiun3Y5OW.wXT8dzRfUO', NULL, '2025-08-20 11:24:58', '2025-08-20 12:23:30', 'siswa', 'photos/siswa/EeQPtKudMf1UnCi4iJwf1yiP8bQQ05yzVx6G7t0G.jpg');
INSERT INTO `users` VALUES (3, 'ROHMAD ZAINAL ABIDIN', 'rohmad.zainal@gmail.com', NULL, '$2y$12$xNGpiTBmz4THN0heAGsNhemqM8trnDYl6lLHHRZaNOHjLzdSOuROC', NULL, '2025-08-20 11:43:03', '2025-08-25 03:08:02', 'guru', 'photos/guru/A3zxXBoWzTdlI2cKlWQF6jSch9dESC6MNzbBD80A.jpg');
INSERT INTO `users` VALUES (4, 'Mega Kurnia Sari', 'mega@gmail.com', NULL, '$2y$12$97bpMAHAAzLaKAgAkY7FweemW56bxdlzXu7GpP/JIUthwtbZ1tVkK', NULL, '2025-08-20 12:30:20', '2025-08-20 12:30:20', 'siswa', 'photos/siswa/HYUW7yY1d61Kw2Yndg38x8pHbPg1jF9zDvuz2ry9.jpg');
INSERT INTO `users` VALUES (5, 'meyza', 'meyza@gmail.com', NULL, '$2y$12$6hCOY7VIYhhYnOoZVhO44uTB8lCy1UcDFkWYjXSJqUUWVYBOrjnuC', NULL, '2025-08-22 11:10:50', '2025-08-22 11:10:50', 'siswa', 'photos/siswa/vRnHxWAJTDNAOSYz33FswHssOIGEBvQOHi4kkImb.jpg');
INSERT INTO `users` VALUES (6, 'Andik Ali Musthofa', 'andik@gmail.com', NULL, '$2y$12$UyKOohaOufFrBHC36vPZHOlB/2qCqOWBBffaqxGuhLaOu8LXu7OUC', NULL, '2025-08-23 03:53:59', '2025-08-23 03:54:15', 'guru', 'photos/guru/HejBGipL6maiqDaswHCrySL8dLfA3vkeKIh9upnS.jpg');
INSERT INTO `users` VALUES (7, 'Edi purnomo', 'edi@contoh.com', NULL, '$2y$12$DOL52iYYjapn18VW/qTKludLCa9nHtw1HyDJjQ6GE1JbhOv6./Vze', NULL, '2025-08-23 03:55:26', '2025-08-23 03:56:45', 'guru', 'photos/guru/b9DrtOkQL2GrUFsOwDrpLMBI9F9C7ZJVfOW2gU9s.jpg');
INSERT INTO `users` VALUES (8, 'adi karya putra', 'adi@tpm.com', NULL, '$2y$12$skBmL2C1t79/0XwJ57ujReG8d.bLYIsfN74jc8/AURgUUlTMO2wpy', NULL, '2025-08-25 03:34:55', '2025-08-25 03:34:55', 'siswa', NULL);
INSERT INTO `users` VALUES (9, 'Denny Darco', 'denny@tpm.com', NULL, '$2y$12$XfB0f44ot7NE0ZJTv9l7Yu3H9hv7W6QkzYOwxAjAzzR3H/gzcLtEi', NULL, '2025-08-25 03:36:35', '2025-08-25 03:36:35', 'siswa', NULL);
INSERT INTO `users` VALUES (10, 'TEKNIK PEMESINAN', 'admin@tpm.com', NULL, '$2y$12$p0CVKFr9AZvyZjQWgUfPXODk5SuLL8/qCIsgiUCAsg5Bl9pBUGjLy', NULL, '2025-08-25 03:45:52', '2025-08-25 03:45:52', 'admin', NULL);
INSERT INTO `users` VALUES (11, 'TEKNIK KENDARAAN RINGAN', 'admin@tkr.com', NULL, '$2y$12$xHIi.GVE7FEOJBhAWzWhT.DPMfzLugp.h33edepzVQ65b0SR1HKV6', NULL, '2025-08-25 03:46:38', '2025-08-25 03:46:38', 'admin', NULL);
INSERT INTO `users` VALUES (12, 'TEKNIK AUDIO VIDEO', 'admin@tav.com', NULL, '$2y$12$FZhd8WAlTUDVqkm0LM63zuL8R14oqF1CU1oBZC.0UB37PLcA9xyuW', NULL, '2025-08-25 03:47:53', '2025-08-25 03:47:53', 'admin', NULL);
INSERT INTO `users` VALUES (13, 'TEKNIK INSTALASI TENAGA LISTRIK', 'admin@titl.com', NULL, '$2y$12$zih96MgiJlAmI.WBPwRuLuELb7FolnNjYpU5FdPxoGVFNPqxhXcpW', NULL, '2025-08-25 03:48:53', '2025-08-25 03:48:53', 'admin', NULL);
INSERT INTO `users` VALUES (14, 'FARMASI', 'admin@far.com', NULL, '$2y$12$faUfCWdW4jKp2reHVOX8MeQQScWGC51kFq310x3ybb1lrwfc9W5UK', NULL, '2025-08-25 03:53:57', '2025-08-25 03:53:57', 'admin', NULL);
INSERT INTO `users` VALUES (15, 'KEPERAWATAN', 'admin@kep.com', NULL, '$2y$12$rXSWg7p2vrB21oqnHqvFRe3k8LAYwe5wTAbsoSVbza23UhHYP1qR2', NULL, '2025-08-25 03:54:25', '2025-08-25 03:54:25', 'admin', NULL);
INSERT INTO `users` VALUES (16, 'PERBANKAN', 'admin@pbk.com', NULL, '$2y$12$CngEIEA0uxCjm0oY5Hlek.AJwJ.Ot5zH6O5E6v6/G/EWmTq/1vw5m', NULL, '2025-08-25 03:55:21', '2025-08-25 03:55:21', 'admin', NULL);
INSERT INTO `users` VALUES (17, 'PERHOTELAN', 'admin@pht.com', NULL, '$2y$12$AW5Af1exer7vlhsaLhcV3uV80aHnRiRfDcEiGbBvHobxFR1gEudli', NULL, '2025-08-25 03:55:59', '2025-08-25 03:55:59', 'admin', NULL);

SET FOREIGN_KEY_CHECKS = 1;
