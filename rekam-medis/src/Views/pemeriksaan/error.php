<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card card-custom p-4 text-center">
            <div class="my-3">
                <i class="bi bi-exclamation-octagon text-danger display-1"></i>
            </div>
            <h4 class="fw-bold text-danger mb-3">Terjadi Kesalahan / Konflik Data</h4>
            <p class="text-muted mb-4"><?= htmlspecialchars($message ?? 'Tidak dapat memproses pemeriksaan medis.') ?></p>
            <div>
                <a href="/antrean" class="btn btn-primary px-4"><i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean Poli</a>
            </div>
        </div>
    </div>
</div>
