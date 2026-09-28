# Dokumentasi Spesifikasi API Mini Hospital EAI

Seluruh API yang membutuhkan autentikasi wajib menyertakan header:
`X-API-KEY: ganti-dengan-kunci-rahasia` (atau sesuai file `.env`).

---

## 1. Sistem Pendaftaran (Port 8001)

### `GET /api/health`
- **Auth**: Tidak perlu
- **Response**: `200 OK`
  ```json
  { "status": "success", "message": "Sistem Pendaftaran operational", "timestamp": "2026-09-28T14:00:00+07:00" }
  ```

### `GET /api/kunjungan?tanggal=&poli=&status=`
- **Auth**: Mandatory `X-API-KEY`
- **Response**: `200 OK`
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
        "pasien": {
          "id_pasien": 1,
          "no_rm": "RM-000001",
          "nama": "Budi Santoso",
          "tgl_lahir": "1988-05-15",
          "jenis_kelamin": "L",
          "alamat": "Jl. Merdeka No. 45"
        }
      }
    ]
  }
  ```

### `GET /api/pasien/{id_pasien}`
- **Auth**: Mandatory `X-API-KEY`
- **Response**: `200 OK` / `404 Not Found`

### `PATCH /api/kunjungan/{id_kunjungan}/status`
- **Auth**: Mandatory `X-API-KEY`
- **Body**: `{ "status": "selesai" }`
- **Response**: `200 OK` / `409 Conflict` (jika status bukan menunggu)

---

## 2. Sistem Farmasi (Port 8003)

### `GET /api/health`
- **Response**: `200 OK`

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
- **Response**: `201 Created` / `409 Conflict` (id_resep duplikat) / `422 Unprocessable` (nama obat tidak ada di master)

### `GET /api/resep?id_rm=`
- **Response**: `200 OK` Array item resep.

### `GET /api/resep/{id_resep}`
- **Response**: `200 OK` Detail 1 item resep / `404 Not Found`.

### `PATCH /api/resep/{id_resep}/status`
- **Body**: `{ "status": "selesai" }`
- **Response**: `200 OK` (stok berkurang) / `409 Conflict` (stok kurang).

### `GET /api/obat?q=`
- **Response**: `200 OK` List master obat.

---

## 3. Sistem Rekam Medis (Port 8002)

### `GET /api/health`
- **Response**: `200 OK`
