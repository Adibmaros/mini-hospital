<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-white-50">Antrean Hari Ini (Menunggu)</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalAntrean ?></h2>
                </div>
                <i class="bi bi-person-lines-fill display-4 text-white-50"></i>
            </div>
            <a href="/antrean" class="text-white mt-3 d-inline-block text-decoration-none">Panggil / Periksa Pasien <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-indigo text-white" style="background-color: #4f46e5;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-white-50">Total Rekam Medis Tersimpan</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalRm ?></h2>
                </div>
                <i class="bi bi-journal-check display-4 text-white-50"></i>
            </div>
            <a href="/riwayat" class="text-white mt-3 d-inline-block text-decoration-none">Cek Riwayat Rekam Medis <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Pasien Menunggu Pemeriksaan Hari Ini</h5>
        <a href="/antrean" class="btn btn-outline-primary btn-sm">Lihat Semua Antrean</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No. Antrean</th>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Poli</th>
                    <th class="text-center">Aksi Dokter</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($antreanList)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada antrean berstatus MENUNGGU hari ini</td></tr>
                <?php else: ?>
                    <?php foreach ($antreanList as $a): ?>
                        <tr>
                            <td><span class="badge bg-secondary fs-6">#<?= sprintf('%02d', $a['no_antrean']) ?></span></td>
                            <td><code><?= htmlspecialchars($a['pasien']['no_rm'] ?? '-') ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($a['pasien']['nama'] ?? '-') ?></td>
                            <td><span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($a['poli']) ?></span></td>
                            <td class="text-center">
                                <a href="/pemeriksaan/<?= $a['id_kunjungan'] ?>" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-stethoscope me-1"></i> Periksa Pasien
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
