<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="/antrean" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <h4 class="fw-bold mb-0">Pemeriksaan Pasien Poli <?= htmlspecialchars($kunjungan['poli']) ?></h4>
                </div>
                <span class="badge bg-secondary fs-6">Antrean #<?= sprintf('%02d', $kunjungan['no_antrean']) ?></span>
            </div>

            <!-- Identitas Pasien (Otomatis dari Pendaftaran API - Tanpa Form Input Pasien) -->
            <div class="bg-light p-3 rounded mb-4 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-vcard me-1"></i> Data Pasien (Integrasi REST API Pendaftaran)</h6>
                    <span class="badge bg-success">GET /api/kunjungan</span>
                </div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Nomor RM</small>
                        <code class="fs-6"><?= htmlspecialchars($pasien['no_rm']) ?></code>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Nama Pasien</small>
                        <strong class="fs-6 text-dark"><?= htmlspecialchars($pasien['nama']) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Tanggal Lahir / Gender</small>
                        <small><?= htmlspecialchars($pasien['tgl_lahir']) ?> (<?= $pasien['jenis_kelamin'] === 'L' ? 'Laki-Laki' : 'Perempuan' ?>)</small>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Alamat</small>
                        <small><?= htmlspecialchars($pasien['alamat']) ?></small>
                    </div>
                </div>
            </div>

            <form method="POST" action="/pemeriksaan/store">
                <?= \App\Core\Csrf::input() ?>
                <input type="hidden" name="id_kunjungan" value="<?= $kunjungan['id_kunjungan'] ?>">
                <input type="hidden" name="id_pasien" value="<?= $pasien['id_pasien'] ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Dokter Pemeriksa <span class="text-danger">*</span></label>
                    <select name="id_dokter" class="form-select" required>
                        <option value="">-- Pilih Dokter --</option>
                        <?php foreach ($dokterList as $d): ?>
                            <option value="<?= $d['id_dokter'] ?>">
                                <?= htmlspecialchars($d['nama_dokter']) ?> (Spesialis <?= htmlspecialchars($d['spesialis']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Keluhan Pasien <span class="text-danger">*</span></label>
                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Tuliskan keluhan utama pasien..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Diagnosa Dokter <span class="text-danger">*</span></label>
                    <textarea name="diagnosa" class="form-control" rows="3" placeholder="Tuliskan hasil diagnosa pemeriksaan..." required></textarea>
                </div>

                <!-- Section Resep Digital (dikirim ke Farmasi) -->
                <div class="border rounded p-3 mb-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-success mb-0"><i class="bi bi-prescription2 me-1"></i> Resep Obat Digital (Integrasi API Farmasi)</h6>
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnAddObat">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                        </button>
                    </div>

                    <?php if (!$farmasiOnline): ?>
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Sistem Farmasi sedang offline / tidak terjangkau. Resep tetap tersimpan di RM dan dapat dikirim ulang nanti.
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tableResep">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Nama Obat (Dropdown Master Farmasi)</th>
                                    <th style="width: 20%;">Dosis</th>
                                    <th style="width: 15%;">Jumlah</th>
                                    <th style="width: 25%;">Aturan Pakai</th>
                                    <th style="width: 5%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="resepContainer">
                                <!-- Initial row -->
                                <tr>
                                    <td>
                                        <select name="obat_nama[]" class="form-select">
                                            <option value="">-- Pilih Obat --</option>
                                            <?php foreach ($obatMaster as $o): ?>
                                                <option value="<?= htmlspecialchars($o['nama_obat']) ?>">
                                                    <?= htmlspecialchars($o['nama_obat']) ?> (Stok: <?= $o['stok'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="obat_dosis[]" class="form-control" placeholder="Misal: 500 mg"></td>
                                    <td><input type="number" name="obat_jumlah[]" class="form-control" min="1" value="1"></td>
                                    <td><input type="text" name="obat_aturan[]" class="form-control" placeholder="Misal: 3x1 sesudah makan"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/antrean" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Examination & Kirim Resep</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAdd = document.getElementById('btnAddObat');
    const container = document.getElementById('resepContainer');

    btnAdd.addEventListener('click', function() {
        const firstRow = container.querySelector('tr');
        if (firstRow) {
            const newRow = firstRow.cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => {
                if (input.type === 'number') input.value = '1';
                else input.value = '';
            });
            newRow.querySelector('select').selectedIndex = 0;
            container.appendChild(newRow);
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            if (container.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });
});
</script>
