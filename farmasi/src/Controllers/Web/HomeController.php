<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Response;
use App\Repositories\ObatRepository;
use App\Repositories\ResepMasukRepository;

class HomeController
{
    public function index(): void
    {
        $obatRepo = new ObatRepository();
        $resepRepo = new ResepMasukRepository();

        $obats = $obatRepo->getAll();
        $resepMenunggu = $resepRepo->getAllGrouped('menunggu');
        $resepAll = $resepRepo->getAllGrouped();

        $lowStockCount = 0;
        foreach ($obats as $o) {
            if ((int)$o['stok'] < 10) {
                $lowStockCount++;
            }
        }

        Response::render('home/index', [
            'totalObat' => count($obats),
            'lowStockCount' => $lowStockCount,
            'totalResepMenunggu' => count($resepMenunggu),
            'totalResep' => count($resepAll),
            'latestResep' => array_slice($resepMenunggu, 0, 5)
        ], 'Dashboard - Sistem Farmasi');
    }
}
