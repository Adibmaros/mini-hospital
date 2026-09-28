<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-prescription2 text-success me-2"></i> Resep Masuk</h3>
        <p class="text-muted mb-0">Daftar resep dari Rekam Medis (Poli) dikelompokkan per kunjungan rekam medis</p>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="/resep" class="row g-3">
        <div class="col-md-10">
            <select name="status" class="form-select">
                <option value="">-- Semua Status Resep --</option>
                <option value="menunggu" <?= $statusFilter === 'menunggu' ? 'selected' : '' ?>>Menunggu Penyiapan</option>
                <option value="selesai" <?= $statusFilter === 'selesai' ? 'selected' : '' ?>>Selesai Disiapkan</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-dark"><i class="bi bi-filter me-1"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID RM</th>
                    <th>No. RM Pasien</th>
                    <th>Nama Pasien</th>
                    <th>Tgl Masuk Resep</th>
                    <th>Rincian Item Obat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($groupedResep)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada resep masuk yang sesuai.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($groupedResep as $g): ?>
                        <tr>
                            <td><span class="badge bg-dark fs-6">RM #<?= $g['id_rm'] ?></span></td>
                            <td><code><?= htmlspecialchars($g['pasien_no_rm']) ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($g['pasien_nama']) ?></td>
                            <td><?= htmlspecialchars($g['tgl_masuk']) ?></td>
                            <td>
                                <?php foreach ($g['items'] as $it): ?>
                                    <div class="mb-1">
                                        <span class="badge bg-<?= $it['status'] === 'menunggu' ? 'warning text-dark' : 'success' ?> me-1">
                                            <?= strtoupper($it['status']) ?>
                                        </span>
                                        <strong><?= htmlspecialchars($it['nama_obat']) ?></strong> (<?= htmlspecialchars($it['dosis']) ?>) &times; <?= $it['jumlah'] ?>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-center">
                                <a href="/resep/<?= $g['id_rm'] ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-eye me-1"></i> Detail / Penyiapan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
