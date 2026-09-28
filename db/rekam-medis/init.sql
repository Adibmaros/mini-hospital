-- Database: db_rekam_medis
CREATE DATABASE IF NOT EXISTS db_rekam_medis DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_rekam_medis;

CREATE TABLE IF NOT EXISTS dokter (
  id_dokter    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_dokter  VARCHAR(100) NOT NULL,
  spesialis    VARCHAR(50)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rekam_medis (
  id_rm         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_kunjungan  INT UNSIGNED NOT NULL UNIQUE,
  id_pasien     INT UNSIGNED NOT NULL,
  id_dokter     INT UNSIGNED NOT NULL,
  keluhan       TEXT NOT NULL,
  diagnosa      TEXT NOT NULL,
  tgl_periksa   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rm_dokter FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter),
  KEY idx_rm_pasien (id_pasien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resep (
  id_resep      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_rm         INT UNSIGNED NOT NULL,
  nama_obat     VARCHAR(100) NOT NULL,
  dosis         VARCHAR(50)  NOT NULL,
  jumlah        INT UNSIGNED NOT NULL,
  aturan_pakai  VARCHAR(100) NOT NULL,
  CONSTRAINT fk_resep_rm FOREIGN KEY (id_rm) REFERENCES rekam_medis(id_rm) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data Dokter
INSERT INTO dokter (id_dokter, nama_dokter, spesialis) VALUES
(1, 'dr. Andi Wijaya', 'Umum'),
(2, 'drg. Maya Indah', 'Gigi'),
(3, 'dr. Rian Hidayat, Sp.A', 'Anak')
ON DUPLICATE KEY UPDATE id_dokter=id_dokter;
