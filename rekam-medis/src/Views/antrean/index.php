<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-people-fill text-indigo me-2"></i> Daftar Antrean Poli Hari Ini</h3>
        <p class="text-muted mb-0">Diambil secara terintegrasi via REST API dari Sistem Pendaftaran (port 8001)</p>
    </div>
</div>

<?php if (!$isOnline): ?>
    <div class="alert alert-warning shadow-sm mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> Sistem Pendaftaran tidak dapat dihubungi. <?= htmlspecialchars($errorMessage ?? '') ?>
    </div>
<?php endif; ?>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="/antrean" class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted">Tanggal Kunjungan</label>
            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Pilih Poli</label>
            <select name="poli" class="form-select">
                <option value="">-- Semua Poli --</option>
                <option value="Umum" <?= $poli === 'Umum' ? 'selected' : '' ?>>Poli Umum</option>
                <option value="Gigi" <?= $poli === 'Gigi' ? 'selected' : '' ?>>Poli Gigi</option>
                <option value="Anak" <?= $poli === 'Anak' ? 'selected' : '' ?>>Poli Anak</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Status Antrean</label>
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
                    <th>No. RM Pasien</th>
                    <th>Nama Pasien</th>
                    <th>Tgl Lahir / Gender</th>
                    <th>Poli</th>
                    <th>Status</th>
                    <th class="text-center">Aksi Dokter</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($antreanList)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data antrean yang sesuai dengan filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($antreanList as $a): ?>
                        <tr>
                            <td><span class="badge bg-secondary fs-6 px-3 py-2">#<?= sprintf('%02d', $a['no_antrean']) ?></span></td>
                            <td><code><?= htmlspecialchars($a['pasien']['no_rm'] ?? '-') ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($a['pasien']['nama'] ?? '-') ?></td>
                            <td>
                                <?= htmlspecialchars($a['pasien']['tgl_lahir'] ?? '-') ?>
                                <span class="badge bg-outline-dark border text-dark ms-1"><?= htmlspecialchars($a['pasien']['jenis_kelamin'] ?? '-') ?></span>
                            </td>
                            <td><span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($a['poli']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $a['status'] === 'menunggu' ? 'warning text-dark' : 'success' ?> fs-6">
                                    <?= strtoupper($a['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($a['status'] === 'menunggu'): ?>
                                    <a href="/pemeriksaan/<?= $a['id_kunjungan'] ?>" class="btn btn-sm btn-primary px-3">
                                        <i class="bi bi-stethoscope me-1"></i> Periksa Pasien
                                    </a>
                                <?php else: ?>
                                    <a href="/riwayat/<?= $a['pasien']['id_pasien'] ?? 0 ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-journal-medical me-1"></i> Lihat RM
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
