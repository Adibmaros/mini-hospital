<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-journal-check text-primary me-2"></i> Daftar Kunjungan & Antrean</h3>
        <p class="text-muted mb-0">Pantau status antrean pasien di masing-masing Poliklinik</p>
    </div>
    <a href="/kunjungan/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Buat Kunjungan Baru</a>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="/kunjungan" class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted">Tanggal Kunjungan</label>
            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Poli Tujuan</label>
            <select name="poli" class="form-select">
                <option value="">-- Semua Poli --</option>
                <option value="Umum" <?= $poli === 'Umum' ? 'selected' : '' ?>>Poli Umum</option>
                <option value="Gigi" <?= $poli === 'Gigi' ? 'selected' : '' ?>>Poli Gigi</option>
                <option value="Anak" <?= $poli === 'Anak' ? 'selected' : '' ?>>Poli Anak</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="menunggu" <?= $status === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter me-1"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No. Antrean</th>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Tgl Kunjungan</th>
                    <th>Poli</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kunjunganList)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada antrean / kunjungan yang sesuai dengan filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kunjunganList as $k): ?>
                        <tr>
                            <td><span class="badge bg-secondary fs-6 px-3 py-2">#<?= sprintf('%02d', $k['no_antrean']) ?></span></td>
                            <td><code><?= htmlspecialchars($k['no_rm']) ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($k['nama']) ?></td>
                            <td><?= htmlspecialchars($k['tgl_kunjungan']) ?></td>
                            <td><span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($k['poli']) ?></span></td>
                            <td>
                                <span class="badge <?= $k['status'] === 'menunggu' ? 'badge-status-menunggu' : 'badge-status-selesai' ?> fs-6">
                                    <i class="bi bi-<?= $k['status'] === 'menunggu' ? 'clock' : 'check-circle' ?> me-1"></i>
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
