# Dokumentasi Rencana & Hasil Pengujian (Test Cases)

## 1. Tabel Matriks Uji End-to-End (Skenario E1 – E14)

| # | Skenario Uji | Langkah Pengujian | Hasil yang Diharapkan | Status |
| --- | --- | --- | --- | --- |
| **E1** | Inisialisasi Lingkungan Docker | Jalankan `docker compose up --build` dari kondisi bersih. | Semua service `running`, DB terisi seed data, 3 UI terbuka di 8001, 8002, 8003. | **Lulus** |
| **E2** | Pendaftaran Pasien & Kunjungan Baru | Akses `:8001`, daftarkan pasien baru & buat kunjungan Poli Umum. | No. RM di-generate otomatis (`RM-XXXXXX`); No. Antrean berurutan (+1). | **Lulus** |
| **E3** | Integrasi Antrean Poli | Akses `:8002/antrean`, filter Poli Umum & tanggal hari ini. | Pasien baru dari E2 muncul otomatis tanpa input ulang data pasien. | **Lulus** |
| **E4** | Examination & Peresepan Digital | Akses `:8002`, isi keluhan, diagnosa, pilih 2 obat dari master Farmasi, simpan. | Rekam medis tersimpan lokal; resep terkirim ke Farmasi via `POST /api/resep` (status: menunggu). | **Lulus** |
| **E5** | Penerimaan Resep di Farmasi | Akses `:8003/resep`, buka resep dari E4. | Resep tampil lengkap dengan nama pasien (diambil via API Pendaftaran), dosis, jumlah, & aturan pakai. | **Lulus** |
| **E6** | Penyiapan Obat & Pemotongan Stok | Akses `:8003/resep/{id_rm}`, klik "Tandai Selesai" pada item obat. | Status resep berubah menjadi "selesai", stok obat di `db_farmasi` berkurang sesuai jumlah. | **Lulus** |
| **E7** | Monitoring Status Resep oleh Dokter | Akses `:8002/riwayat/{id_pasien}`. | Dokter melihat status item resep telah ter-update menjadi "selesai". | **Lulus** |
| **E8** | Update Status Kunjungan Pendaftaran (A6) | Akses `:8001/kunjungan`. | Status kunjungan pasien otomatis berubah dari "menunggu" menjadi "selesai". | **Lulus** |
| **E9** | Pencegahan Duplikasi Rekam Medis (1 Visit = 1 RM) | Coba simpan rekam medis kedua untuk kunjungan yang sama. | Ditolak dengan response HTTP `409 Conflict`. | **Lulus** |
| **E10** | Ketahanan Saat Farmasi Offline | Hentikan container `farmasi`, lalu dokter menyimpan pemeriksaan baru di `:8002`. | Rekam medis & resep lokal tetap tersimpan; status di UI "Belum terkirim"; app tidak crash. | **Lulus** |
| **E11** | Idempotensi Kirim Ulang Resep | Nyalakan container `farmasi`, klik "Kirim Ulang" di `:8002/riwayat`. | Resep masuk ke Farmasi tepat satu kali (HTTP 409 dianggap sukses, mencegah duplikasi item). | **Lulus** |
| **E12** | Validasi Stok Kurang di Farmasi | Di `:8003`, tandai selesai untuk obat yang stoknya < jumlah diminta. | Ditolak dengan HTTP `409 Conflict` ("Stok tidak mencukupi"), status & stok tidak berubah. | **Lulus** |
| **E13** | Autentikasi API Key (X-API-KEY) | Panggil endpoint `/api/pasien/1` atau `/api/resep` tanpa header `X-API-KEY`. | Ditolak dengan HTTP `401 Unauthorized`. | **Lulus** |
| **E14** | Validasi Nama Obat Master (A5) | Kirim `POST /api/resep` dengan `nama_obat` fiktif yang tidak ada di master. | Ditolak dengan HTTP `422 Unprocessable Entity`. | **Lulus** |

---

## 2. Cara Menjalankan Automated Smoke Test

Gunakan skrip `scripts/smoke-test.sh` untuk melakukan pengujian otomatis terhadap seluruh endpoint API utama:

```bash
./scripts/smoke-test.sh
```

Skrip ini akan memeriksa HTTP status code pada tiap langkah dan keluar dengan **Exit Code 0** jika seluruh skenario uji lulus.
