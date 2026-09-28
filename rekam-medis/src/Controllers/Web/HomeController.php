<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Response;
use App\Clients\PendaftaranClient;
use App\Repositories\RekamMedisRepository;

class HomeController
{
    public function index(): void
    {
        $pendaftaranClient = new PendaftaranClient();
        $rmRepo = new RekamMedisRepository();

        $today = date('Y-m-d');
        $kunjunganRes = $pendaftaranClient->getKunjungan($today, null, 'menunggu');
        $antreanHariIni = $kunjunganRes['success'] ? ($kunjunganRes['data'] ?? []) : [];

        $allRm = $rmRepo->getAll();

        Response::render('home/index', [
            'pendaftaranOnline' => $kunjunganRes['success'],
            'totalAntrean' => count($antreanHariIni),
            'totalRm' => count($allRm),
            'antreanList' => array_slice($antreanHariIni, 0, 5)
        ], 'Dashboard - Sistem Rekam Medis (Poli)');
    }
}
