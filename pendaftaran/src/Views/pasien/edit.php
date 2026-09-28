<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <a href="/pasien" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                <h4 class="fw-bold mb-0">Edit Data Pasien</h4>
            </div>

            <form method="POST" action="/pasien/<?= $pasien['id_pasien'] ?>/update">
                <?= \App\Core\Csrf::input() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Rekam Medis</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($pasien['no_rm']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($pasien['nama']) ?>" required>
                    <?php if (isset($errors['nama'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nama']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_lahir" class="form-control <?= isset($errors['tgl_lahir']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($pasien['tgl_lahir']) ?>" required>
                        <?php if (isset($errors['tgl_lahir'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['tgl_lahir']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" required>
                            <option value="L" <?= $pasien['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                            <option value="P" <?= $pasien['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <?php if (isset($errors['jenis_kelamin'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['jenis_kelamin']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat" class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>" rows="3" required><?= htmlspecialchars($pasien['alamat']) ?></textarea>
                    <?php if (isset($errors['alamat'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['alamat']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nomor Handphone / WA</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($pasien['no_hp'] ?? '') ?>">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/pasien" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Perbarui Data Pasien</button>
                </div>
            </form>
        </div>
    </div>
</div>
