# Panduan Skenario Demo Presentasi (5–7 Menit)

Dokumen ini berisi panduan langkah demi langkah saat melakukan demo presentasi di depan kelas/penguji.

---

## ⏱ Timeline Demo (Total: 6 Menit)

### Menit 0:00 – 01:00 | Persiapan & Arsitektur
1. Tunjukkan terminal Docker: `docker compose up --build`.
2. Jelaskan singkat bahwa 3 aplikasi PHP Native berjalan terpisah di port **8001** (Pendaftaran), **8002** (Rekam Medis), dan **8003** (Farmasi) dengan 3 database terpisah.
3. Tunjukkan **Swagger UI** (`http://localhost:8081`) sebagai kontrak API terpusat.

### Menit 01:00 – 02:30 | Alur 1: Pendaftaran Pasien (Port 8001)
1. Buka `http://localhost:8001`. Tunjukkan daftar pasien default.
2. Klik **Registrasi Pasien Baru**. Isi nama misal: `Rudi Hermawan`, tanggal lahir, gender, alamat.
3. Klik **Simpan**. Tekankan bahwa **No. RM (`RM-000006`) dibuat otomatis** oleh sistem.
4. Klik **Buat Kunjungan Baru**. Pilih `Rudi Hermawan`, pilih **Poli Umum**.
5. Tunjukkan bahwa pasien mendapatkan **No. Antrean #03** berstatus `MENUNGGU`.

### Menit 02:30 – 04:30 | Alur 2: Pemeriksaan Poli / Dokter (Port 8002)
1. Buka `http://localhost:8002/antrean`. Filter Poli Umum.
2. Tekankan poin utama EAI: **Dokter tidak perlu menginput ulang data pasien `Rudi Hermawan`**. Data diambil via API `GET /api/kunjungan`.
3. Klik **Periksa Pasien**.
4. Pilih Dokter (`dr. H. Ahmad Dahlan`), isi Keluhan (*Demam tinggi 2 hari, pusing*), Diagnosa (*Febris / Influenza*).
5. Pada bagian Resep Obat, tunjukkan dropdown obat yang diambil langsung dari Master Farmasi. Tambahkan 2 obat:
   - `Paracetamol 500 mg` - Dosis `500 mg` - Jumlah `10` - Aturan `3x1 sesudah makan`
   - `Vitamin C 500 mg` - Dosis `500 mg` - Jumlah `5` - Aturan `1x1`
6. Klik **Simpan Examination & Kirim Resep**.

### Menit 04:30 – 05:30 | Alur 3: Penyiapan Obat di Farmasi (Port 8003)
1. Buka `http://localhost:8003/resep`.
2. Tunjukkan bahwa resep `Rudi Hermawan` langsung masuk secara **real-time** (< 1 detik).
3. Klik **Detail / Penyiapan**. Tunjukkan nama pasien terisi via API `GET /api/pasien/{id}` ke Pendaftaran.
4. Klik **Tandai Selesai** pada `Paracetamol 500 mg`. Tunjukkan stok obat di Master Obat otomatis berkurang 10 unit.

### Menit 05:30 – 06:30 | Alur 4: Verifikasi Integrasi & Fallback
1. Kembali ke Rekam Medis (`http://localhost:8002/riwayat`). Buka riwayat `Rudi Hermawan`. Tunjukkan status resep di Farmasi ter-update menjadi `SELESAI`.
2. Kembali ke Pendaftaran (`http://localhost:8001/kunjungan`). Tunjukkan status kunjungan `Rudi Hermawan` otomatis berubah menjadi `SELESAI` (A6).
3. Demonstrasikan **Smoke Test**: Jalankan `./scripts/smoke-test.sh` di terminal. Tunjukkan pesan **All Smoke Tests Passed Successfully! (Exit Code 0)**.
