<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <a href="/pasien" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                <h4 class="fw-bold mb-0">Form Registrasi Pasien Baru</h4>
            </div>

            <form method="POST" action="/pasien/store">
                <?= \App\Core\Csrf::input() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Rekam Medis (Otomatis)</label>
                    <input type="text" name="no_rm" class="form-control bg-light" value="<?= htmlspecialchars($nextNoRm) ?>" readonly>
                    <small class="text-muted">No. RM dibuat otomatis oleh sistem (FR-PD-01)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" placeholder="Contoh: Budi Santoso" value="<?= htmlspecialchars($old['nama'] ?? '') ?>" required>
                    <?php if (isset($errors['nama'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nama']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_lahir" class="form-control <?= isset($errors['tgl_lahir']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['tgl_lahir'] ?? '') ?>" required>
                        <?php if (isset($errors['tgl_lahir'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['tgl_lahir']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" <?= ($old['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                            <option value="P" <?= ($old['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <?php if (isset($errors['jenis_kelamin'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['jenis_kelamin']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat" class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>" rows="3" placeholder="Jl. Merdeka No. 45..." required><?= htmlspecialchars($old['alamat'] ?? '') ?></textarea>
                    <?php if (isset($errors['alamat'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['alamat']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nomor Handphone / WA</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="081234567890" value="<?= htmlspecialchars($old['no_hp'] ?? '') ?>">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/pasien" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data Pasien</button>
                </div>
            </form>
        </div>
    </div>
</div>
