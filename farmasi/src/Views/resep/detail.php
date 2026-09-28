<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="/resep" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <h4 class="fw-bold mb-0">Detail Penyiapan Resep [RM #<?= $idRm ?>]</h4>
                </div>
                <span class="badge bg-primary fs-6">Integrasi API Pendaftaran</span>
            </div>

            <div class="row g-3 bg-light p-3 rounded mb-4">
                <div class="col-md-4">
                    <small class="text-muted d-block">Nama Pasien</small>
                    <strong class="fs-5 text-dark"><?= htmlspecialchars($pasien['nama'] ?? 'Pasien #' . $items[0]['id_pasien']) ?></strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Nomor Rekam Medis</small>
                    <code><?= htmlspecialchars($pasien['no_rm'] ?? '-') ?></code>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Tanggal Lahir / Alamat</small>
                    <small><?= htmlspecialchars($pasien['tgl_lahir'] ?? '-') ?> | <?= htmlspecialchars($pasien['alamat'] ?? '-') ?></small>
                </div>
            </div>

            <h5 class="fw-bold mb-3"><i class="bi bi-list-check me-2 text-success"></i> Daftar Item Obat pada Resep</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Dosis</th>
                            <th>Jumlah</th>
                            <th>Aturan Pakai</th>
                            <th>Status Penyiapan</th>
                            <th class="text-center">Aksi Apoteker</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $idx => $it): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($it['nama_obat']) ?></td>
                                <td><?= htmlspecialchars($it['dosis']) ?></td>
                                <td><span class="badge bg-secondary fs-6"><?= $it['jumlah'] ?> unit</span></td>
                                <td><code><?= htmlspecialchars($it['aturan_pakai']) ?></code></td>
                                <td>
                                    <span class="badge <?= $it['status'] === 'menunggu' ? 'badge-status-menunggu' : 'badge-status-selesai' ?> fs-6">
                                        <?= strtoupper($it['status']) ?>
                                    </span>
                                    <?php if ($it['status'] === 'selesai' && !empty($it['tgl_selesai'])): ?>
                                        <small class="d-block text-muted"><?= htmlspecialchars($it['tgl_selesai']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($it['status'] === 'menunggu'): ?>
                                        <form method="POST" action="/resep/<?= $it['id_resep'] ?>/status" class="d-inline">
                                            <?= \App\Core\Csrf::input() ?>
                                            <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Tandai obat ini selesai disiapkan? Stok obat akan berkurang.')">
                                                <i class="bi bi-check-lg me-1"></i> Tandai Selesai
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Sudah Disiapkan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
