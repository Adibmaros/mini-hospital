-- Database: db_pendaftaran
CREATE DATABASE IF NOT EXISTS db_pendaftaran DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pendaftaran;

CREATE TABLE IF NOT EXISTS pasien (
  id_pasien     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  no_rm         VARCHAR(20)  NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  tgl_lahir     DATE         NOT NULL,
  jenis_kelamin ENUM('L','P') NOT NULL,
  alamat        VARCHAR(255) NOT NULL,
  no_hp         VARCHAR(20)  NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kunjungan (
  id_kunjungan  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pasien     INT UNSIGNED NOT NULL,
  tgl_kunjungan DATE         NOT NULL,
  poli          VARCHAR(50)  NOT NULL,
  no_antrean    INT UNSIGNED NOT NULL,
  status        ENUM('menunggu','selesai') NOT NULL DEFAULT 'menunggu',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_kunjungan_pasien FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE CASCADE,
  UNIQUE KEY uq_antrean (tgl_kunjungan, poli, no_antrean),
  KEY idx_kunjungan_filter (tgl_kunjungan, poli, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data
INSERT INTO pasien (id_pasien, no_rm, nama, tgl_lahir, jenis_kelamin, alamat, no_hp) VALUES
(1, 'RM-000001', 'Budi Santoso', '1988-05-15', 'L', 'Jl. Merdeka No. 45, Jakarta Pusat', '081298765432'),
(2, 'RM-000002', 'Dewi Lestari', '1992-11-20', 'P', 'Jl. Sudirman No. 12, Jakarta Selatan', '081311223344'),
(3, 'RM-000003', 'Siti Aminah', '1995-04-12', 'P', 'Jl. Gatot Subroto No. 88, Bandung', '081234567890'),
(4, 'RM-000004', 'Ahmad Rizki', '2001-08-03', 'L', 'Jl. Ahmad Yani No. 5, Surabaya', '085678901234'),
(5, 'RM-000005', 'Eka Putri', '1990-01-30', 'P', 'Jl. Diponegoro No. 10, Yogyakarta', '087890123456')
ON DUPLICATE KEY UPDATE id_pasien=id_pasien;

INSERT INTO kunjungan (id_kunjungan, id_pasien, tgl_kunjungan, poli, no_antrean, status) VALUES
(1, 1, CURDATE(), 'Umum', 1, 'menunggu'),
(2, 3, CURDATE(), 'Umum', 2, 'menunggu'),
(3, 2, CURDATE(), 'Gigi', 1, 'menunggu')
ON DUPLICATE KEY UPDATE id_kunjungan=id_kunjungan;
