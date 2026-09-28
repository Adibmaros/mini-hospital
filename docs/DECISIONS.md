# Log Keputusan Arsitektur & Penyesuaian Requirement (DECISIONS.md)

Dokumen ini mencatat seluruh keputusan teknik, asumsi, serta penyesuaian (A1–A6) yang diambil untuk menutup celah pada dokumen requirement awal (`Mini_Hospital_EAI.docx`).

---

## 1. Log Penyesuaian Requirement (A1 – A6)

| ID | Penyesuaian | Prioritas | Alasan & Keputusan Implementasi |
| --- | --- | --- | --- |
| **A1** | Penambahan `PATCH /api/resep/{id_resep}/status` di Farmasi | **Wajib (M)** | Dokumen awal menyebut apoteker harus mengubah status "selesai", namun tidak menyediakan endpoint update. Dibuat endpoint PATCH yang aman dalam transaksi DB (`SELECT FOR UPDATE`). |
| **A2** | Penambahan kolom `dosis` & `aturan_pakai` di `resep_masuk` | **Wajib (M)** | Apoteker membutuhkan dosis dan aturan pakai untuk mencetak etiket obat. Kolom ini ditambahkan ke skema `resep_masuk`. |
| **A3** | Penambahan kolom `id_rm` & `tgl_masuk` di `resep_masuk` | **Wajib (M)** | Satu rekam medis dapat memiliki beberapa item obat. Kolom `id_rm` memungkinkan pengelompokan item obat per rekam medis di UI Farmasi. |
| **A4** | Konsumsi `GET /api/pasien/{id}` oleh Farmasi | **Wajib (M)** | `resep_masuk` hanya menyimpan `id_pasien`. Farmasi memanggil API Pendaftaran untuk mengambil nama pasien (dengan fallback "Pasien #id" jika Pendaftaran offline). |
| **A5** | Master Obat Dropdown & Validasi (`GET /api/obat`) | **Sebaiknya (S)** | Mencegah salah ketik nama obat (masalah P4). App Rekam Medis mengambil dropdown obat dari Farmasi. `POST /api/resep` menolak (422) jika nama obat tidak ada di master. |
| **A6** | Auto Update Status Kunjungan (`PATCH /api/kunjungan/{id}/status`) | **Sebaiknya (S)** | Setelah dokter selesai menyimpan pemeriksaan di Rekam Medis, status kunjungan di Pendaftaran otomatis diperbarui dari `menunggu` menjadi `selesai`. |

---

## 2. Keputusan Teknik & Kebijakan Keamanan

1. **Monorepo Structure**: Menggunakan 1 folder induk yang mengoperasikan 3 aplikasi PHP native terpisah dalam Docker Compose demi kemudahan deployment & verifikasi.
2. **Tanpa Framework & Composer**: Seluruh class di-load secara otomatis menggunakan `spl_autoload_register` native PHP 8.2 untuk memenuhi constraint tantangan PRD.
3. **Prepared Statements & CSRF**: Seluruh query database menggunakan PDO Prepared Statements untuk mencegah SQL Injection, dan seluruh form HTML POST menggunakan token CSRF (`Csrf::verify()`).
4. **Keamanan Autentikasi API**: Seluruh pemanggilan REST API diwajibkan menyertakan header `X-API-KEY`.
