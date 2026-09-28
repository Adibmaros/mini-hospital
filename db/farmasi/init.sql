-- Database: db_farmasi
CREATE DATABASE IF NOT EXISTS db_farmasi DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_farmasi;

CREATE TABLE IF NOT EXISTS obat (
  id_obat    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_obat  VARCHAR(100) NOT NULL UNIQUE,
  stok       INT UNSIGNED NOT NULL DEFAULT 0,
  harga      DECIMAL(12,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resep_masuk (
  id_resep_masuk INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_resep       INT UNSIGNED NOT NULL UNIQUE,
  id_rm          INT UNSIGNED NOT NULL,
  id_pasien      INT UNSIGNED NOT NULL,
  nama_obat      VARCHAR(100) NOT NULL,
  dosis          VARCHAR(50)  NOT NULL,
  jumlah         INT UNSIGNED NOT NULL,
  aturan_pakai   VARCHAR(100) NOT NULL,
  status         ENUM('menunggu','selesai') NOT NULL DEFAULT 'menunggu',
  tgl_masuk      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tgl_selesai    DATETIME NULL,
  KEY idx_rm (id_rm),
  KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data Obat
INSERT INTO obat (id_obat, nama_obat, stok, harga) VALUES
(1, 'Paracetamol 500 mg', 100, 5000.00),
(2, 'Amoxicillin 500 mg', 50, 12000.00),
(3, 'Cetirizine 10 mg', 80, 8000.00),
(4, 'Ibuprofen 400 mg', 60, 9500.00),
(5, 'Antasida Doen', 40, 6000.00),
(6, 'Vitamin C 500 mg', 150, 4500.00),
(7, 'Ambroxol 30 mg', 70, 7500.00),
(8, 'Omeprazole 20 mg', 30, 15000.00),
(9, 'ORS Oralit', 100, 3000.00),
(10, 'Chlorhexidine Mouthwash', 25, 25000.00)
ON DUPLICATE KEY UPDATE id_obat=id_obat;
