<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-white-50">Total Pasien Terdaftar</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalPasien ?></h2>
                </div>
                <i class="bi bi-person-vcard display-4 text-white-50"></i>
            </div>
            <a href="/pasien" class="text-white mt-3 d-inline-block text-decoration-none">Lihat Semua Pasien <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-info text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-dark-50">Kunjungan Hari Ini</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalKunjunganToday ?></h2>
                </div>
                <i class="bi bi-calendar-event display-4 text-dark-50"></i>
            </div>
            <a href="/kunjungan" class="text-dark mt-3 d-inline-block text-decoration-none fw-semibold">Lihat Antrean Hari Ini <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Antrean Terbaru Hari Ini</h5>
                <a href="/kunjungan/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Buat Kunjungan Baru</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No. Antrean</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Poli</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kunjunganHariIni)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada kunjungan hari ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($kunjunganHariIni as $k): ?>
                                <tr>
                                    <td><span class="badge bg-secondary fs-6">#<?= sprintf('%02d', $k['no_antrean']) ?></span></td>
                                    <td><code><?= htmlspecialchars($k['no_rm']) ?></code></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($k['nama']) ?></td>
                                    <td><span class="badge bg-outline-dark border text-dark"><?= htmlspecialchars($k['poli']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $k['status'] === 'menunggu' ? 'badge-status-menunggu' : 'badge-status-selesai' ?>">
                                            <?= strtoupper($k['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-warning"></i> Aksi Cepat</h5>
            <div class="d-grid gap-2">
                <a href="/pasien/create" class="btn btn-outline-primary py-2 text-start">
                    <i class="bi bi-person-plus-fill me-2"></i> Pendaftaran Pasien Baru
                </a>
                <a href="/kunjungan/create" class="btn btn-outline-success py-2 text-start">
                    <i class="bi bi-ticket-perforated-fill me-2"></i> Buat Kunjungan / Antrean
                </a>
                <a href="/pasien" class="btn btn-outline-secondary py-2 text-start">
                    <i class="bi bi-search me-2"></i> Cari Data Pasien
                </a>
            </div>
        </div>
    </div>
</div>
