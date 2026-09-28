<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <a href="/kunjungan" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                <h4 class="fw-bold mb-0">Pendaftaran Kunjungan Pasien</h4>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/kunjungan/store">
                <?= \App\Core\Csrf::input() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Pasien <span class="text-danger">*</span></label>
                    <select name="id_pasien" class="form-select" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php foreach ($pasienList as $p): ?>
                            <option value="<?= $p['id_pasien'] ?>" <?= ($selectedPasien && $selectedPasien['id_pasien'] == $p['id_pasien']) ? 'selected' : '' ?>>
                                [<?= htmlspecialchars($p['no_rm']) ?>] <?= htmlspecialchars($p['nama']) ?> - <?= htmlspecialchars($p['alamat']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_kunjungan" class="form-control" value="<?= htmlspecialchars($today) ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Poli Tujuan <span class="text-danger">*</span></label>
                    <select name="poli" class="form-select" required>
                        <option value="">-- Pilih Poliklinik --</option>
                        <option value="Umum">Poli Umum</option>
                        <option value="Gigi">Poli Gigi</option>
                        <option value="Anak">Poli Anak</option>
                    </select>
                    <small class="text-muted d-block mt-1">Nomor antrean akan di-generate otomatis per poli untuk tanggal terpilih.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/kunjungan" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-ticket-perforated me-1"></i> Buat Kunjungan & Ambil Antrean</button>
                </div>
            </form>
        </div>
    </div>
</div>
