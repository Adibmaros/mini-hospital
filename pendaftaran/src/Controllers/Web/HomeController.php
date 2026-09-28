<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Response;
use App\Repositories\PasienRepository;
use App\Repositories\KunjunganRepository;

class HomeController
{
    public function index(): void
    {
        $pasienRepo = new PasienRepository();
        $kunjunganRepo = new KunjunganRepository();

        $today = date('Y-m-d');
        $pasienList = $pasienRepo->getAll();
        $kunjunganToday = $kunjunganRepo->getAll($today);

        Response::render('home/index', [
            'totalPasien' => count($pasienList),
            'totalKunjunganToday' => count($kunjunganToday),
            'kunjunganHariIni' => array_slice($kunjunganToday, 0, 5)
        ], 'Dashboard - Sistem Pendaftaran');
    }
}
