<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <a href="/obat" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                <h4 class="fw-bold mb-0">Tambah Obat Baru</h4>
            </div>

            <form method="POST" action="/obat/store">
                <?= \App\Core\Csrf::input() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Obat <span class="text-danger">*</span></label>
                    <input type="text" name="nama_obat" class="form-control <?= isset($errors['nama_obat']) ? 'is-invalid' : '' ?>" placeholder="Contoh: Paracetamol 500 mg" value="<?= htmlspecialchars($old['nama_obat'] ?? '') ?>" required>
                    <?php if (isset($errors['nama_obat'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_obat']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>" placeholder="100" min="0" value="<?= htmlspecialchars($old['stok'] ?? '0') ?>" required>
                        <?php if (isset($errors['stok'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['stok']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="harga" class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>" placeholder="5000" min="0" value="<?= htmlspecialchars($old['harga'] ?? '0') ?>" required>
                        <?php if (isset($errors['harga'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['harga']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/obat" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i> Simpan Obat</button>
                </div>
            </form>
        </div>
    </div>
</div>
