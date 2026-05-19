-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for quiz_elektronika
CREATE DATABASE IF NOT EXISTS `quiz_elektronika` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `quiz_elektronika`;

-- Dumping structure for table quiz_elektronika.jalur_jawaban
CREATE TABLE IF NOT EXISTS `jalur_jawaban` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_soal` int unsigned NOT NULL,
  `titik_a_id` int unsigned NOT NULL,
  `titik_b_id` int unsigned NOT NULL,
  `style` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'straight',
  `control_points` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jalur_jawaban_id_soal_foreign` (`id_soal`),
  KEY `jalur_jawaban_titik_a_id_foreign` (`titik_a_id`),
  KEY `jalur_jawaban_titik_b_id_foreign` (`titik_b_id`),
  CONSTRAINT `jalur_jawaban_id_soal_foreign` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jalur_jawaban_titik_a_id_foreign` FOREIGN KEY (`titik_a_id`) REFERENCES `titik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jalur_jawaban_titik_b_id_foreign` FOREIGN KEY (`titik_b_id`) REFERENCES `titik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.jalur_jawaban: ~0 rows (approximately)

-- Dumping structure for table quiz_elektronika.jawaban_user
CREATE TABLE IF NOT EXISTS `jawaban_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned NOT NULL,
  `id_soal` int unsigned NOT NULL,
  `titik_a_id` int unsigned NOT NULL,
  `titik_b_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jawaban_user_id_user_foreign` (`id_user`),
  KEY `jawaban_user_id_soal_foreign` (`id_soal`),
  KEY `jawaban_user_titik_a_id_foreign` (`titik_a_id`),
  KEY `jawaban_user_titik_b_id_foreign` (`titik_b_id`),
  CONSTRAINT `jawaban_user_id_soal_foreign` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_user_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_user_titik_a_id_foreign` FOREIGN KEY (`titik_a_id`) REFERENCES `titik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_user_titik_b_id_foreign` FOREIGN KEY (`titik_b_id`) REFERENCES `titik` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.jawaban_user: ~0 rows (approximately)

-- Dumping structure for table quiz_elektronika.kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `icon` varchar(50) DEFAULT 'fas fa-folder',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table quiz_elektronika.kategori: ~0 rows (approximately)

-- Dumping structure for table quiz_elektronika.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.migrations: ~1 rows (approximately)
INSERT IGNORE INTO `migrations` (`id`, `version`, `class`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
	(1, '2024_01_01_000001', 'App\\Database\\Migrations\\CreateQuizTables', 'default', 'App', 1778526704, 1);

-- Add ukuran column to titik table for existing databases
ALTER TABLE `titik` ADD COLUMN IF NOT EXISTS `ukuran` int unsigned NOT NULL DEFAULT 24 AFTER `label`;

-- Dumping structure for table quiz_elektronika.soal
CREATE TABLE IF NOT EXISTS `soal` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_kategori` int DEFAULT NULL,
  `nama_soal` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `img_width` int unsigned NOT NULL,
  `img_height` int unsigned NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.soal: ~0 rows (approximately)

-- Dumping structure for table quiz_elektronika.titik
CREATE TABLE IF NOT EXISTS `titik` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_soal` int unsigned NOT NULL,
  `x` decimal(20,6) NOT NULL DEFAULT (0),
  `y` decimal(20,6) NOT NULL DEFAULT (0),
  `label` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ukuran` int unsigned NOT NULL DEFAULT 24,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `titik_id_soal_foreign` (`id_soal`),
  CONSTRAINT `titik_id_soal_foreign` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.titik: ~0 rows (approximately)

-- Dumping structure for table quiz_elektronika.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table quiz_elektronika.users: ~3 rows (approximately)
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `nama_lengkap`, `role`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'admin@quiz.com', '$2y$10$3J9B5Q8Bp4eCloAuGX7czeQE9NArFhV1omt.QqJ1SEBoyl3vDE3za', 'Administrator', 'admin', 'aktif', '2026-05-11 19:11:53', NULL),
	(2, 'user1', 'user1@quiz.com', '$2y$10$AiVvMCupOCryQ31qsn4zqu6nh.0gEwbtO8guhf3b/94kW2ztAK/Ki', 'User Satu', 'user', 'aktif', '2026-05-11 19:11:53', '2026-05-12 00:12:11'),
	(3, 'user2', 'user2@quiz.com', '$2y$10$AiVvMCupOCryQ31qsn4zqu6nh.0gEwbtO8guhf3b/94kW2ztAK/Ki', 'User Dua', 'user', 'aktif', '2026-05-11 21:49:36', '2026-05-11 21:49:36');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
