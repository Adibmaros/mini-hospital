<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-dark-50">Resep Menunggu</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalResepMenunggu ?></h2>
                </div>
                <i class="bi bi-hourglass-split display-4 text-dark-50"></i>
            </div>
            <a href="/resep?status=menunggu" class="text-dark mt-3 d-inline-block text-decoration-none fw-semibold">Proses Resep <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-4 bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-white-50">Total Obat Master</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $totalObat ?></h2>
                </div>
                <i class="bi bi-capsule display-4 text-white-50"></i>
            </div>
            <a href="/obat" class="text-white mt-3 d-inline-block text-decoration-none">Kelola Obat <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-4 bg-<?= $lowStockCount > 0 ? 'danger' : 'secondary' ?> text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase tracking-wide text-white-50">Stok Rendah (< 10)</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $lowStockCount ?></h2>
                </div>
                <i class="bi bi-exclamation-triangle display-4 text-white-50"></i>
            </div>
            <a href="/obat" class="text-white mt-3 d-inline-block text-decoration-none">Cek Stok <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-prescription2 text-success me-2"></i> Resep Masuk Terbaru (Menunggu Penyiapan)</h5>
        <a href="/resep" class="btn btn-outline-success btn-sm">Lihat Semua Resep</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID RM</th>
                    <th>ID Pasien</th>
                    <th>Waktu Masuk</th>
                    <th>Jumlah Obat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($latestResep)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada resep berstatus menunggu saat ini</td></tr>
                <?php else: ?>
                    <?php foreach ($latestResep as $r): ?>
                        <tr>
                            <td><span class="badge bg-dark fs-6">RM #<?= $r['id_rm'] ?></span></td>
                            <td>Pasien #<?= $r['id_pasien'] ?></td>
                            <td><?= htmlspecialchars($r['tgl_masuk']) ?></td>
                            <td><span class="badge bg-secondary"><?= count($r['items']) ?> item</span></td>
                            <td class="text-center">
                                <a href="/resep/<?= $r['id_rm'] ?>" class="btn btn-sm btn-success px-3">
                                    <i class="bi bi-eye me-1"></i> Buka Detail Resep
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
