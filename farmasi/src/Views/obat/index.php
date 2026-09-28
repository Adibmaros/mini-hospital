<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-boxes text-success me-2"></i> Master Obat</h3>
        <p class="text-muted mb-0">Kelola inventaris obat, harga, dan pantau stok obat</p>
    </div>
    <a href="/obat/create" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Tambah Obat Baru</a>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="/obat" class="row g-3">
        <div class="col-md-10">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 bg-light" placeholder="Cari nama obat..." value="<?= htmlspecialchars($query ?? '') ?>">
            </div>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-dark">Cari</button>
        </div>
    </form>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID Obat</th>
                    <th>Nama Obat</th>
                    <th>Stok Tersedia</th>
                    <th>Harga (Rp)</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($obats)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada data obat yang ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($obats as $o): ?>
                        <tr>
                            <td><code>#<?= $o['id_obat'] ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($o['nama_obat']) ?></td>
                            <td>
                                <span class="badge bg-<?= (int)$o['stok'] < 10 ? 'danger' : 'success' ?> fs-6">
                                    <?= (int)$o['stok'] ?> unit
                                </span>
                                <?php if ((int)$o['stok'] < 10): ?>
                                    <small class="text-danger ms-1"><i class="bi bi-exclamation-circle"></i> Stok Menipis!</small>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold">Rp <?= number_format((float)$o['harga'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="/obat/<?= $o['id_obat'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit / Restok
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
