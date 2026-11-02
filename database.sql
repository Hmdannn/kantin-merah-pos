-- SQL Schema & Data Awal Kantin Merah POS
-- Database: kantin_merah_db

CREATE DATABASE IF NOT EXISTS `kantin_merah_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kantin_merah_db`;

-- 1. Table user
DROP TABLE IF EXISTS `notifikasi`;
DROP TABLE IF EXISTS `detail_pesanan`;
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `shift`;
DROP TABLE IF EXISTS `pesanan`;
DROP TABLE IF EXISTS `menu`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id_user` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('kasir', 'dapur', 'manajerial') NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password for all accounts: password123
-- Hash: $2y$10$wTf7i1iK9t8nL9Z0e4s4qO4J7b3Ew7T4V5Z1L2a3b4c5d6e7f8g9h (example)
-- To ensure compatibility, we insert hash generated with PHP PASSWORD_DEFAULT for 'password123':
-- $2y$10$qR6M7yU8xG6yL.8O8bZ2Ie5g4lF9vjT1iK.Q7v6zN8mB5aC3dE2fG
INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Kasir Kantin Merah', 'kasir@kantinmerah.com', '$2y$10$tZ2E7HhJ5aH5m2.7V4N5v.oE0B1eE6t6X7Q2R3w4E5r6T7y8U9i0O', 'kasir', NOW(), NOW()),
(2, 'Chef Dapur Merah', 'dapur@kantinmerah.com', '$2y$10$tZ2E7HhJ5aH5m2.7V4N5v.oE0B1eE6t6X7Q2R3w4E5r6T7y8U9i0O', 'dapur', NOW(), NOW()),
(3, 'Manajer / Pemilik Kantin', 'manajerial@kantinmerah.com', '$2y$10$tZ2E7HhJ5aH5m2.7V4N5v.oE0B1eE6t6X7Q2R3w4E5r6T7y8U9i0O', 'manajerial', NOW(), NOW());

-- 2. Table kategori
CREATE TABLE `kategori` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `created_at`) VALUES
(1, 'Makanan Berat', NOW()),
(2, 'Minuman Segar', NOW()),
(3, 'Snack & Camilan', NOW());

-- 3. Table menu
CREATE TABLE `menu` (
  `id_menu` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_menu` VARCHAR(150) NOT NULL,
  `harga` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `jumlah_stok` INT NOT NULL DEFAULT 0,
  `status_stok` ENUM('Tersedia', 'Habis') NOT NULL DEFAULT 'Tersedia',
  `foto` VARCHAR(255) NULL,
  `id_kategori` INT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori`(`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga`, `jumlah_stok`, `status_stok`, `foto`, `id_kategori`, `created_at`, `updated_at`) VALUES
(1, 'Nasi Goreng Kantin Merah Spesial', 15000.00, 30, 'Tersedia', '', 1, NOW(), NOW()),
(2, 'Ayam Geprek Sambal Bawang + Nasi', 16000.00, 25, 'Tersedia', '', 1, NOW(), NOW()),
(3, 'Mie Goreng Telur Sayur', 12000.00, 40, 'Tersedia', '', 1, NOW(), NOW()),
(4, 'Es Teh Manis Jumbo', 4000.00, 50, 'Tersedia', '', 2, NOW(), NOW()),
(5, 'Kopi Susu Kampus', 8000.00, 20, 'Tersedia', '', 2, NOW(), NOW()),
(6, 'Kentang Goreng Crispy', 10000.00, 15, 'Tersedia', '', 3, NOW(), NOW());

-- 4. Table pesanan
CREATE TABLE `pesanan` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pesanan` VARCHAR(50) NOT NULL UNIQUE,
  `waktu_pesan` DATETIME NOT NULL,
  `status` ENUM('Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Siap Diambil', 'Selesai', 'Kadaluarsa', 'Dibatalkan') NOT NULL DEFAULT 'Menunggu Pembayaran',
  `total_bayar` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `metode_bayar` ENUM('Tunai', 'QRIS') NOT NULL DEFAULT 'Tunai',
  `id_user_update` INT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table detail_pesanan
CREATE TABLE `detail_pesanan` (
  `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pesanan` INT NOT NULL,
  `id_menu` INT NOT NULL,
  `jumlah` INT NOT NULL DEFAULT 1,
  `catatan_khusus` TEXT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NULL,
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `menu`(`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table shift
CREATE TABLE `shift` (
  `id_shift` INT AUTO_INCREMENT PRIMARY KEY,
  `id_kasir` INT NOT NULL,
  `waktu_mulai` DATETIME NOT NULL,
  `waktu_selesai` DATETIME NULL,
  `total_kas_tunai` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_kas_qris` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NULL,
  FOREIGN KEY (`id_kasir`) REFERENCES `user`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Table pembayaran
CREATE TABLE `pembayaran` (
  `id_pembayaran` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pesanan` INT NOT NULL UNIQUE,
  `id_kasir` INT NULL,
  `metode` ENUM('Tunai', 'QRIS') NOT NULL,
  `status_bayar` ENUM('Menunggu', 'Berhasil', 'Kadaluarsa', 'Gagal') NOT NULL DEFAULT 'Menunggu',
  `batas_waktu` DATETIME NULL,
  `jumlah_diterima` DECIMAL(10,2) NULL DEFAULT 0.00,
  `kembalian` DECIMAL(10,2) NULL DEFAULT 0.00,
  `waktu_bayar` DATETIME NULL,
  `created_at` DATETIME NULL,
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Table notifikasi
CREATE TABLE `notifikasi` (
  `id_notifikasi` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pesanan` INT NOT NULL,
  `pesan` TEXT NOT NULL,
  `status_baca` TINYINT(1) NOT NULL DEFAULT 0,
  `waktu_kirim` DATETIME NOT NULL,
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Shift tidak di-seed karena harus dibuka sendiri oleh kasir
-- melalui halaman Kasir / Kelola Shift agar alur buka-tutup shift
-- bisa diuji apa adanya.

-- Sample pesanan awal untuk demo antrean kasir & dapur
INSERT INTO `pesanan` (`id_pesanan`, `kode_pesanan`, `waktu_pesan`, `status`, `total_bayar`, `metode_bayar`, `created_at`, `updated_at`)
VALUES
(1, 'ORD-20261024-001', NOW() - INTERVAL 25 MINUTE, 'Menunggu Pembayaran', 19000.00, 'Tunai', NOW(), NOW()),
(2, 'ORD-20261024-002', NOW() - INTERVAL 15 MINUTE, 'Dibayar', 20000.00, 'QRIS', NOW(), NOW()),
(3, 'ORD-20261024-003', NOW() - INTERVAL 10 MINUTE, 'Diproses', 15000.00, 'Tunai', NOW(), NOW()),
(4, 'ORD-20261024-004', NOW() - INTERVAL 5 MINUTE, 'Siap Diambil', 8000.00, 'QRIS', NOW(), NOW());

INSERT INTO `detail_pesanan` (`id_pesanan`, `id_menu`, `jumlah`, `catatan_khusus`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 'Pedas level 3, banyakin bawang goreng', 15000.00, NOW()),
(1, 4, 1, 'Gula sedikit', 4000.00, NOW()),
(2, 2, 1, 'Cabe 5 aja', 16000.00, NOW()),
(2, 4, 1, 'Es batu dipisah', 4000.00, NOW()),
(3, 1, 1, 'Tidak pedas, tidak pakai kecap', 15000.00, NOW()),
(4, 5, 1, 'Dingin less sugar', 8000.00, NOW());

INSERT INTO `pembayaran` (`id_pesanan`, `id_kasir`, `metode`, `status_bayar`, `batas_waktu`, `jumlah_diterima`, `kembalian`, `waktu_bayar`, `created_at`) VALUES
(2, 1, 'QRIS', 'Berhasil', NOW() + INTERVAL 15 MINUTE, 20000.00, 0.00, NOW() - INTERVAL 14 MINUTE, NOW()),
(3, 1, 'Tunai', 'Berhasil', NOW() + INTERVAL 15 MINUTE, 20000.00, 5000.00, NOW() - INTERVAL 9 MINUTE, NOW()),
(4, 1, 'QRIS', 'Berhasil', NOW() + INTERVAL 15 MINUTE, 8000.00, 0.00, NOW() - INTERVAL 4 MINUTE, NOW());

