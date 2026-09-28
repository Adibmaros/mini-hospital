# Dokumentasi Pola Integrasi & Analisis EAI (Enterprise Application Integration)

## 1. Gaya Integrasi yang Digunakan

Proyek Mini Hospital ini mengimplementasikan gaya integrasi **Point-to-Point Synchronous REST API** dengan pertukaran pesan bertipe **JSON**.

### Alasan Pemilihan:
1. **Kemudahan Implementasi & Overhead Rendah**: Menggunakan HTTP/JSON standar tanpa memerlukan infrastruktur tambahan seperti message broker (RabbitMQ/Kafka) atau Service Bus.
2. **Kebutuhan Real-Time**: Dokter membutuhkan data antrean dan identitas pasien secara langsung (*synchronous*) saat pemeriksaan.

---

## 2. Analisis Kelebihan & Kekurangan Architecture

| Aspek | Pola Point-to-Point REST (Saat Ini) | Opsi Masa Depan (Event-Driven / Message Broker) |
| --- | --- | --- |
| **Coupling (Keterikatan)** | **Tinggi (Tight Coupling)**: Rekam Medis harus mengetahui lokasi endpoint URL Farmasi dan Pendaftaran. | **Rendah (Loose Coupling)**: Servis mengirimkan event ke broker tanpa perlu tahu konsumennya. |
| **Ketersediaan (Availability)** | **Terpengaruh**: Jika Farmasi mati saat peresepan, pengiriman otomatis gagal (walau ada mekanisme fallback lokal & retry). | **Sangat Tinggi**: Pesan disimpan di antrean broker hingga Farmasi kembali online. |
| **Skalabilitas** | Sedang (Perlu load balancer jika traffic sangat tinggi). | Sangat Tinggi (Dapat memproses jutaan event secara asinkron). |
| **Kompleksitas Ops** | Sangat Rendah (Cukup Apache/PHP & MySQL). | Sedang-Tinggi (Memerlukan cluster RabbitMQ/Kafka & monitoring consumer worker). |

---

## 3. Pemetaan Masalah Bisnis (P1–P4) ke Solusi Integrasi

| # | Masalah Asli Studi Kasus | Solusi EAI Terimplementasi | Hasil / Bukti Terverifikasi |
| --- | --- | --- | --- |
| **P1** | Data pasien diinput berulang di pendaftaran & poli | Pola *System of Record*: Pendaftaran adalah pemilik tunggal data pasien. Rekam medis memanggil `GET /api/kunjungan` & `GET /api/pasien/{id}`. | App Rekam Medis **tidak memiliki form input pasien**. Data terisi otomatis. |
| **P2** | Dokter tidak bisa melihat antrean real-time | Rekam Medis mengambil antrean langsung dari API Pendaftaran secara real-time. | Antrean Poli = Data Pendaftaran saat itu juga. |
| **P3** | Resep kertas rawan hilang / tulisan tak terbaca | Peresepan digital dikirim langsung backend-to-backend via `POST /api/resep` ke Sistem Farmasi. | Resep muncul di Farmasi < 1 detik setelah dokter menyimpan pemeriksaan. |
| **P4** | Data antar bagian tidak konsisten (nama obat, RM) | No. RM dibuat otomatis di Pendaftaran. Rekam Medis memilih nama obat dari master Farmasi (`GET /api/obat`), dan Farmasi menolak obat yang tidak valid (`422`). | Nama obat divalidasi 100% konsisten terhadap master obat Farmasi. |

---

## 4. Saran & Rekomendasi Pengembangan Masa Depan (Future Roadmap)

1. **Penerapan Message Broker (RabbitMQ / Apache Kafka)**:
   Mengubah pengiriman resep dari HTTP synchronous ke event-driven (`PrescriptionCreatedEvent`) agar peresepan tetap berjalan 100% tanpa hambatan bahkan saat Sistem Farmasi sedang maintenance.
2. **Implementasi Outbox Pattern**:
   Guna menjamin *guaranteed delivery* (at-least-once delivery) saat terjadi kegagalan jaringan antar servis.
3. **API Gateway (Kong / Traefik)**:
   Sebagai pintu masuk tunggal (*Single Point of Entry*) yang menangani Rate Limiting, Authentication (OAuth2/JWT), Logging, dan Circuit Breaker.
4. **Single Sign-On (SSO) & RBAC**:
   Menambahkan layanan identitas terpusat (Keycloak / Keycloak OpenID Connect) untuk autentikasi pengguna UI (Dokter, Apoteker, Petugas Pendaftaran).
