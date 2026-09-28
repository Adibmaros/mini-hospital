<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\ObatRepository;

class ApiObatController
{
    private ObatRepository $obatRepo;

    public function __construct()
    {
        $this->obatRepo = new ObatRepository();
    }

    public function index(): void
    {
        $q = Request::getQueryParams()['q'] ?? null;
        $obats = $this->obatRepo->getAll($q);

        $formatted = array_map(function ($o) {
            return [
                'id_obat' => (int)$o['id_obat'],
                'nama_obat' => $o['nama_obat'],
                'stok' => (int)$o['stok'],
                'harga' => (float)$o['harga']
            ];
        }, $obats);

        Response::json([
            'status' => 'success',
            'message' => 'OK',
            'data' => $formatted
        ]);
    }
}
