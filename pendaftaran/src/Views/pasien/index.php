<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-people text-primary me-2"></i> Data Pasien</h3>
        <p class="text-muted mb-0">Kelola master data pasien terdaftar di Mini Hospital</p>
    </div>
    <a href="/pasien/create" class="btn btn-primary"><i class="bi bi-person-plus-fill me-1"></i> Registrasi Pasien Baru</a>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="/pasien" class="row g-3">
        <div class="col-md-10">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 bg-light" placeholder="Cari berdasarkan nama atau Nomor Rekam Medis (RM)..." value="<?= htmlspecialchars($query ?? '') ?>">
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
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Tgl Lahir / Gender</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pasienList)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data pasien yang ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pasienList as $p): ?>
                        <tr>
                            <td><span class="badge bg-dark fs-6"><code><?= htmlspecialchars($p['no_rm']) ?></code></span></td>
                            <td class="fw-bold"><?= htmlspecialchars($p['nama']) ?></td>
                            <td>
                                <?= htmlspecialchars($p['tgl_lahir']) ?>
                                <span class="badge rounded-pill bg-<?= $p['jenis_kelamin'] === 'L' ? 'primary' : 'danger' ?> ms-1">
                                    <?= $p['jenis_kelamin'] === 'L' ? 'Laki-Laki' : 'Perempuan' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['alamat']) ?></td>
                            <td><?= htmlspecialchars($p['no_hp'] ?: '-') ?></td>
                            <td class="text-center">
                                <a href="/kunjungan/create?id_pasien=<?= $p['id_pasien'] ?>" class="btn btn-sm btn-outline-success me-1" title="Daftar Kunjungan">
                                    <i class="bi bi-ticket-perforated"></i> Kunjungan
                                </a>
                                <a href="/pasien/<?= $p['id_pasien'] ?>/edit" class="btn btn-sm btn-outline-primary" title="Edit Pasien">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
