<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Clients\PendaftaranClient;

class AntreanController
{
    private PendaftaranClient $pendaftaranClient;

    public function __construct()
    {
        $this->pendaftaranClient = new PendaftaranClient();
    }

    public function index(): void
    {
        $queryParams = Request::getQueryParams();
        $tanggal = $queryParams['tanggal'] ?? date('Y-m-d');
        $poli = $queryParams['poli'] ?? '';
        $status = $queryParams['status'] ?? 'menunggu';

        $res = $this->pendaftaranClient->getKunjungan($tanggal, $poli ?: null, $status ?: null);

        Response::render('antrean/index', [
            'isOnline' => $res['success'],
            'errorMessage' => $res['success'] ? null : $res['message'],
            'antreanList' => $res['data'] ?? [],
            'tanggal' => $tanggal,
            'poli' => $poli,
            'status' => $status
        ], 'Daftar Antrean Pasien Poli - Rekam Medis');
    }
}
