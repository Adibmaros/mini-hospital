# Dokumentasi Arsitektur Sistem Mini Hospital EAI

## 1. Diagram Konteks (Context Diagram)

```mermaid
flowchart LR
    P[Sistem Pendaftaran<br/>:8001<br/>db_pendaftaran] -- "GET pasien & antrean" --> R[Sistem Rekam Medis<br/>:8002<br/>db_rekam_medis]
    R -- "POST resep" --> F[Sistem Farmasi<br/>:8003<br/>db_farmasi]
    R -- "GET status resep" --> F
    R -. "GET daftar obat (A5)" .-> F
    R -. "PATCH status kunjungan (A6)" .-> P
    F -. "GET data pasien (A4)" .-> P
```

---

## 2. Diagram Komponen Architecture

Setiap aplikasi mengadopsi arsitektur terpisah **Model-View-Controller (MVC)** dengan **Repository Pattern**:

- **Core Component**: Autoloader, Router, Request, Response, Database (PDO Singleton), ErrorHandler terpusat, ApiAuth middleware (`X-API-KEY`), Csrf protection.
- **Repositories**: Mengisolasi query SQL per tabel.
- **Clients (cURL HTTP Client)**: Mengelola komunikasi server-to-server antar backend.
- **Controllers**: Menerima request HTTP (Web UI / REST API) dan memanggil repository/client.

---

## 3. Sequence Diagram Alur Utama End-to-End

```mermaid
sequenceDiagram
    actor Petugas
    actor Dokter
    actor Apoteker
    participant PD as Pendaftaran (:8001)
    participant RM as Rekam Medis (:8002)
    participant FA as Farmasi (:8003)

    Petugas->>PD: Registrasi Pasien & Buat Kunjungan
    PD-->>Petugas: Nomor RM & Nomor Antrean
    Dokter->>RM: Buka Antrean Poli
    RM->>PD: GET /api/kunjungan?tanggal=...&poli=...
    PD-->>RM: JSON List Antrean + Data Pasien
    Dokter->>RM: Simpan Keluhan, Diagnosa & Resep Obat
    RM->>RM: Simpan Local RM & Resep (DB Transaction)
    RM->>FA: POST /api/resep (Resep Digital)
    FA-->>RM: 201 Created (Status: menunggu)
    RM->>PD: PATCH /api/kunjungan/{id}/status = selesai
    Apoteker->>FA: Buka Daftar Resep Masuk
    FA->>PD: GET /api/pasien/{id} (Ambil nama pasien)
    FA-->>Apoteker: Tampilkan Resep & Nama Pasien
    Apoteker->>FA: Tandai Selesai Penyiapan
    FA->>FA: Potong Stok Obat & Set Status Selesai
    Dokter->>RM: Cek Status Riwayat RM
    RM->>FA: GET /api/resep?id_rm=...
    FA-->>RM: Status Resep Terbaru (selesai)
```

---

## 4. System of Record (Sumber Kebenaran Data)

| Entitas Data | System of Record | Penjelasan |
| --- | --- | --- |
| **Pasien & Kunjungan** | Sistem Pendaftaran | Pendaftaran memegang hak tunggal pembuatan No. RM dan No. Antrean. Aplikasi lain mengambil data via API. |
| **Rekam Medis & Resep** | Sistem Rekam Medis | Tempat utama penyimpanan keluhan, diagnosa dokter, dan resep asli. |
| **Master Obat & Stok** | Sistem Farmasi | Tempat utama stok inventaris obat dan penyiapan resep. |
