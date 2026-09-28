# Dokumentasi Spesifikasi API Mini Hospital EAI

Seluruh API yang membutuhkan autentikasi wajib menyertakan header:
`X-API-KEY: ganti-dengan-kunci-rahasia` (atau sesuai konfigurasi file `.env`).

Swagger UI interaktif tersedia di: `http://localhost:8081`

---

## 1. Sistem Pendaftaran (Port 8001)

### `GET /api/health`
- **Auth**: Tidak perlu
- **Response**: `200 OK`
  ```json
  {
    "status": "success",
    "message": "Sistem Pendaftaran operational",
    "timestamp": "2026-09-28T14:00:00+07:00"
  }
  ```

### `GET /api/kunjungan?tanggal=&poli=&status=`
- **Auth**: Mandatory `X-API-KEY`
- **Query Params**:
  - `tanggal` (opsional, default: hari ini format `YYYY-MM-DD`)
  - `poli` (opsional, contoh: `Umum`, `Gigi`, `Anak`)
  - `status` (opsional: `menunggu`, `selesai`)
- **Response**: `200 OK` / `401 Unauthorized`
  ```json
  {
    "status": "success",
    "message": "OK",
    "data": [
      {
        "id_kunjungan": 1,
        "tgl_kunjungan": "2026-09-28",
        "poli": "Umum",
        "no_antrean": 1,
        "status": "menunggu",
        "created_at": "2026-09-28 08:00:00",
        "pasien": {
          "id_pasien": 1,
          "no_rm": "RM-000001",
          "nama": "Budi Santoso",
          "tgl_lahir": "1988-05-15",
          "jenis_kelamin": "L",
          "alamat": "Jl. Merdeka No. 45",
          "no_hp": "081234567890"
        }
      }
    ]
  }
  ```

### `GET /api/kunjungan/{id_kunjungan}`
- **Auth**: Mandatory `X-API-KEY`
- **Path Param**: `id_kunjungan` (integer)
- **Response**: `200 OK` / `401 Unauthorized` / `404 Not Found`
  ```json
  {
    "status": "success",
    "message": "OK",
    "data": {
      "id_kunjungan": 1,
      "tgl_kunjungan": "2026-09-28",
      "poli": "Umum",
      "no_antrean": 1,
      "status": "menunggu",
      "created_at": "2026-09-28 08:00:00",
      "pasien": {
        "id_pasien": 1,
        "no_rm": "RM-000001",
        "nama": "Budi Santoso",
        "tgl_lahir": "1988-05-15",
        "jenis_kelamin": "L",
        "alamat": "Jl. Merdeka No. 45",
        "no_hp": "081234567890"
      }
    }
  }
  ```

### `GET /api/pasien/{id_pasien}`
- **Auth**: Mandatory `X-API-KEY`
- **Response**: `200 OK` / `401 Unauthorized` / `404 Not Found`
  ```json
  {
    "status": "success",
    "message": "OK",
    "data": {
      "id_pasien": 1,
      "no_rm": "RM-000001",
      "nama": "Budi Santoso",
      "tgl_lahir": "1988-05-15",
      "jenis_kelamin": "L",
      "alamat": "Jl. Merdeka No. 45",
      "no_hp": "081234567890",
      "created_at": "2026-09-28 07:30:00"
    }
  }
  ```

### `PATCH /api/kunjungan/{id_kunjungan}/status`
- **Auth**: Mandatory `X-API-KEY`
- **Body**: `{ "status": "selesai" }`
- **Response**:
  - `200 OK`: Status berhasil diubah.
  - `401 Unauthorized`: API Key tidak valid.
  - `404 Not Found`: Kunjungan tidak ditemukan.
  - `409 Conflict`: Status saat ini bukan 'menunggu'.
  - `422 Unprocessable`: Nilai status bukan 'selesai'.

---

## 2. Sistem Farmasi (Port 8003)

### `GET /api/health`
- **Auth**: Tidak perlu
- **Response**: `200 OK`
  ```json
  {
    "status": "success",
    "message": "Sistem Farmasi operational",
    "timestamp": "2026-09-28T14:00:00+07:00"
  }
  ```

### `POST /api/resep`
- **Auth**: Mandatory `X-API-KEY`
- **Body Request**:
  ```json
  {
    "id_rm": 1,
    "id_pasien": 1,
    "items": [
      {
        "id_resep": 10,
        "nama_obat": "Paracetamol 500 mg",
        "dosis": "500 mg",
        "jumlah": 10,
        "aturan_pakai": "3x1 sesudah makan"
      }
    ]
  }
  ```
- **Response**:
  - `201 Created`: Resep berhasil diterima.
  - `401 Unauthorized`: API Key tidak valid.
  - `409 Conflict`: Idempotency fail (`id_resep` duplikat).
  - `422 Unprocessable`: Nama obat tidak ada di master obat atau field tidak lengkap.

### `GET /api/resep?id_rm=`
- **Auth**: Mandatory `X-API-KEY`
- **Query Param**: `id_rm` (mandatory integer)
- **Response**: `200 OK` (Array item resep) / `400 Bad Request` / `401 Unauthorized`

### `GET /api/resep/{id_resep}`
- **Auth**: Mandatory `X-API-KEY`
- **Path Param**: `id_resep` (ID resep lokal Rekam Medis)
- **Response**: `200 OK` (Detail 1 item resep) / `401 Unauthorized` / `404 Not Found` (dianggap belum terkirim oleh Rekam Medis).

### `PATCH /api/resep/{id_resep}/status`
- **Auth**: Mandatory `X-API-KEY`
- **Body**: `{ "status": "selesai" }`
- **Response**:
  - `200 OK`: Resep selesai, stok otomatis berkurang.
  - `401 Unauthorized`: API Key tidak valid.
  - `404 Not Found`: Resep tidak ditemukan.
  - `409 Conflict`: Stok tidak cukup atau transisi status tidak valid.
  - `422 Unprocessable`: Nilai status bukan 'selesai'.

### `GET /api/obat?q=`
- **Auth**: Mandatory `X-API-KEY`
- **Query Param**: `q` (opsional, pencarian nama obat)
- **Response**: `200 OK` (List master obat beserta sisa stok dan harga) / `401 Unauthorized`

---

## 3. Sistem Rekam Medis (Port 8002)

Dalam arsitektur Mini Hospital EAI, Sistem Rekam Medis bertindak sebagai **konsumen API** dari Sistem Pendaftaran dan Sistem Farmasi. Seluruh alur kerja medis dilakukan via **Web UI (Browser)** pada port 8002:
- `/antrean`: Melihat antrean pasien hari ini dari Pendaftaran.
- `/pemeriksaan/{id_kunjungan}`: Input anamnesis, diagnosis, tindakan, dan resep obat yang diteruskan ke Farmasi.
- `/riwayat`: Melihat riwayat pemeriksaan medis pasien.

Satu-satunya REST API publik yang diekspos oleh modul ini adalah:

### `GET /api/health`
- **Auth**: Tidak perlu
- **Response**: `200 OK`
  ```json
  {
    "status": "success",
    "message": "Sistem Rekam Medis operational",
    "timestamp": "2026-09-28T14:00:00+07:00"
  }
  ```
