<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex align-items-center mb-4">
            <a href="/riwayat" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h4 class="fw-bold mb-0">Riwayat Rekam Medis Pasien</h4>
        </div>

        <!-- Profil Pasien dari Pendaftaran API -->
        <div class="card card-custom p-4 mb-4 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-person-circle me-2"></i> <?= htmlspecialchars($pasien['nama']) ?></h5>
                <span class="badge bg-dark fs-6">No. RM: <code><?= htmlspecialchars($pasien['no_rm']) ?></code></span>
            </div>
            <div class="row g-2 text-muted small">
                <div class="col-md-4"><i class="bi bi-calendar3 me-1"></i> Tgl Lahir: <?= htmlspecialchars($pasien['tgl_lahir']) ?></div>
                <div class="col-md-4"><i class="bi bi-gender-ambiguous me-1"></i> Jenis Kelamin: <?= $pasien['jenis_kelamin'] === 'L' ? 'Laki-Laki' : 'Perempuan' ?></div>
                <div class="col-md-4"><i class="bi bi-geo-alt me-1"></i> Alamat: <?= htmlspecialchars($pasien['alamat']) ?></div>
            </div>
        </div>

        <?php if (empty($rmList)): ?>
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-3 d-block mb-2"></i> Pasien belum memiliki data rekam medis.
            </div>
        <?php else: ?>
            <?php foreach ($rmList as $rm): ?>
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <span class="badge bg-primary me-2"><i class="bi bi-calendar-event me-1"></i> <?= htmlspecialchars($rm['tgl_periksa']) ?></span>
                            <strong class="text-dark"><i class="bi bi-person-badge me-1"></i> Dokter: <?= htmlspecialchars($rm['nama_dokter']) ?></strong> (<?= htmlspecialchars($rm['spesialis']) ?>)
                        </div>
                        <span class="badge bg-outline-secondary border text-muted">ID RM #<?= $rm['id_rm'] ?></span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">Keluhan Utama</label>
                            <div class="p-3 bg-light rounded text-dark"><?= nl2br(htmlspecialchars($rm['keluhan'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary mb-1">Diagnosa Dokter</label>
                            <div class="p-3 bg-light rounded text-dark"><?= nl2br(htmlspecialchars($rm['diagnosa'])) ?></div>
                        </div>
                    </div>

                    <?php if (!empty($rm['resep_items'])): ?>
                        <div class="border rounded p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-success mb-0"><i class="bi bi-capsule me-1"></i> Resep Obat & Status Farmasi</h6>
                                <?php
                                    $hasFailed = false;
                                    foreach ($rm['resep_items'] as $it) {
                                        if ($it['status_farmasi'] === 'belum_terkirim') {
                                            $hasFailed = true;
                                            break;
                                        }
                                    }
                                ?>
                                <?php if ($hasFailed): ?>
                                    <form method="POST" action="/pemeriksaan/resend/<?= $rm['id_rm'] ?>" class="d-inline">
                                        <?= \App\Core\Csrf::input() ?>
                                        <button type="submit" class="btn btn-sm btn-warning text-dark px-3 fw-semibold">
                                            <i class="bi bi-send-exclamation-fill me-1"></i> Kirim Ulang Resep ke Farmasi
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th>Dosis</th>
                                        <th>Jumlah</th>
                                        <th>Aturan Pakai</th>
                                        <th>Status di Farmasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rm['resep_items'] as $it): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($it['nama_obat']) ?></td>
                                            <td><?= htmlspecialchars($it['dosis']) ?></td>
                                            <td><?= $it['jumlah'] ?></td>
                                            <td><code><?= htmlspecialchars($it['aturan_pakai']) ?></code></td>
                                            <td>
                                                <?php if ($it['status_farmasi'] === 'selesai'): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                                                <?php elseif ($it['status_farmasi'] === 'menunggu'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Menunggu Farmasi</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Belum Terkirim</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
