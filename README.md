# Mini Hospital Enterprise Application Integration (EAI)

Sistem Integrasi Aplikasi Pendaftaran, Rekam Medis (Poli), dan Farmasi berbasis REST API (PHP 8.2 Native & MySQL 8).

---

## 🚀 Ringkasan Sistem

Mini Hospital EAI terdiri dari tiga aplikasi terpisah yang berkomunikasi secara synchronous via **Point-to-Point REST API**:

| Aplikasi | Service Docker | Port Host | Fungsi Utama |
| --- | --- | --- | --- |
| **Pendaftaran** | `pendaftaran` | `:8001` | Pendaftaran Pasien Baru, Manajemen Kunjungan & Antrean Poli |
| **Rekam Medis (Poli)** | `rekam-medis` | `:8002` | Pemeriksaan Dokter, Diagnosis, Peresepan Obat Digital |
| **Farmasi** | `farmasi` | `:8003` | Penyiapan Obat, Master Obat & Stok Inventaris |
| **phpMyAdmin** | `phpmyadmin` | `:8080` | GUI Manajemen Database MySQL |
| **Swagger UI** | `swagger-ui` | `:8081` | Dokumentasi Interaktif API (OpenAPI 3.0) |

---

## 🛠 Prasyarat

- **Docker Desktop** (versi 20.10+) & Docker Compose (`docker compose`)

---

## 🚀 Cara Menjalankan Aplikasi (3 Langkah)

1. **Salin File Konfigurasi Environment**
   ```bash
   cp .env.example .env
   ```

2. **Jalankan Docker Compose**
   ```bash
   docker compose up --build
   ```

3. **Akses Aplikasi melalui Browser**
   - **Sistem Pendaftaran**: `http://localhost:8001`
   - **Sistem Rekam Medis (Poli)**: `http://localhost:8002`
   - **Sistem Farmasi**: `http://localhost:8003`
   - **phpMyAdmin**: `http://localhost:8080` (Username: `root` / `pendaftaran`, Password sesuai `.env`)
   - **Swagger API Docs**: `http://localhost:8081`

---

## 🧪 Menguji Sistem (Smoke Test)

Jalankan skrip pengujian otomatis (persyaratan exit code 0):

```bash
chmod +x scripts/smoke-test.sh
./scripts/smoke-test.sh
```

---

## 🔄 Reset Data & Seed Ulang

Untuk mengembalikan database ke kondisi awal (seed data default):

```bash
docker compose down -v
docker compose up --build
```

---

## ❓ Troubleshooting & Known Issues

1. **Port Bentrok (`8001`, `8002`, `8003`, `8080`, `8081` sudah dipakai)**:
   Ubah port pemetaan host di `docker-compose.yml` pada bagian `ports:`.
2. **Koneksi Antar Container Gagal**:
   Pastikan menggunakan nama service (`http://pendaftaran`, `http://farmasi`), **bukan `localhost`**, karena antar container berada dalam network `hospital-net`.
3. **Init SQL Tidak Berjalan Ulang**:
   MySQL Docker hanya menjalankan script `.sql` di `/docker-entrypoint-initdb.d/` pada insisialisasi volume kosong pertama kali. Gunakan `docker compose down -v` untuk menghapus volume.
