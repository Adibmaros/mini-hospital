<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-journal-medical text-primary me-2"></i> Riwayat Rekam Medis Pasien</h3>
        <p class="text-muted mb-0">Daftar seluruh pasien yang pernah memiliki catatan rekam medis</p>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Gender / Tgl Lahir</th>
                    <th>Jumlah Pemeriksaan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patientList)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada rekam medis tersimpan.</td></tr>
                <?php else: ?>
                    <?php foreach ($patientList as $item): ?>
                        <?php $p = $item['pasien']; ?>
                        <tr>
                            <td><code><?= htmlspecialchars($p['no_rm'] ?? 'RM-UNKNOWN') ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars($p['nama'] ?? 'Pasien #' . $item['id_pasien']) ?></td>
                            <td><?= htmlspecialchars($p['tgl_lahir'] ?? '-') ?></td>
                            <td><span class="badge bg-indigo text-white px-3 py-2"><?= $item['count_rm'] ?> kali periksa</span></td>
                            <td class="text-center">
                                <a href="/riwayat/<?= $item['id_pasien'] ?>" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-eye me-1"></i> Buka Riwayat Medis
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
