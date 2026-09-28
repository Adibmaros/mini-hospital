<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Repositories\PasienRepository;
use App\Repositories\KunjunganRepository;

class KunjunganController
{
    private KunjunganRepository $kunjunganRepo;
    private PasienRepository $pasienRepo;

    public function __construct()
    {
        $this->kunjunganRepo = new KunjunganRepository();
        $this->pasienRepo = new PasienRepository();
    }

    public function index(): void
    {
        $queryParams = Request::getQueryParams();
        $tanggal = $queryParams['tanggal'] ?? date('Y-m-d');
        $poli = $queryParams['poli'] ?? '';
        $status = $queryParams['status'] ?? '';

        $kunjunganList = $this->kunjunganRepo->getAll($tanggal, $poli ?: null, $status ?: null);

        Response::render('kunjungan/index', [
            'kunjunganList' => $kunjunganList,
            'tanggal' => $tanggal,
            'poli' => $poli,
            'status' => $status
        ], 'Daftar Kunjungan - Sistem Pendaftaran');
    }

    public function create(): void
    {
        $idPasien = (int)(Request::getQueryParams()['id_pasien'] ?? 0);
        $pasienList = $this->pasienRepo->getAll();
        $selectedPasien = $idPasien ? $this->pasienRepo->getById($idPasien) : null;

        Response::render('kunjungan/create', [
            'pasienList' => $pasienList,
            'selectedPasien' => $selectedPasien,
            'error' => null,
            'today' => date('Y-m-d')
        ], 'Pendaftaran Kunjungan Baru - Sistem Pendaftaran');
    }

    public function store(): void
    {
        Csrf::verify();
        $body = Request::getBody();

        $idPasien = (int)($body['id_pasien'] ?? 0);
        $tglKunjungan = $body['tgl_kunjungan'] ?? date('Y-m-d');
        $poli = $body['poli'] ?? '';

        $pasienList = $this->pasienRepo->getAll();
        $selectedPasien = $idPasien ? $this->pasienRepo->getById($idPasien) : null;

        if (!$idPasien || !$selectedPasien) {
            Response::render('kunjungan/create', [
                'pasienList' => $pasienList,
                'selectedPasien' => null,
                'error' => 'Silakan pilih pasien yang valid',
                'today' => $tglKunjungan
            ], 'Pendaftaran Kunjungan Baru - Sistem Pendaftaran');
            return;
        }

        if (empty($poli)) {
            Response::render('kunjungan/create', [
                'pasienList' => $pasienList,
                'selectedPasien' => $selectedPasien,
                'error' => 'Silakan pilih poli tujuan',
                'today' => $tglKunjungan
            ], 'Pendaftaran Kunjungan Baru - Sistem Pendaftaran');
            return;
        }

        // Check pending visit
        if ($this->kunjunganRepo->hasPendingKunjungan($idPasien, $tglKunjungan, $poli)) {
            Response::render('kunjungan/create', [
                'pasienList' => $pasienList,
                'selectedPasien' => $selectedPasien,
                'error' => 'Pasien sudah memiliki antrean berstatus MENUNGGU di Poli ' . htmlspecialchars($poli) . ' untuk tanggal ini.',
                'today' => $tglKunjungan
            ], 'Pendaftaran Kunjungan Baru - Sistem Pendaftaran');
            return;
        }

        $idKunjungan = $this->kunjunganRepo->create([
            'id_pasien' => $idPasien,
            'tgl_kunjungan' => $tglKunjungan,
            'poli' => $poli,
            'status' => 'menunggu'
        ]);

        Response::redirect('/kunjungan?tanggal=' . $tglKunjungan . '&msg=Kunjungan+berhasil+didaftarkan');
    }
}
