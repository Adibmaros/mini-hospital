<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Response;
use App\Repositories\PasienRepository;

class ApiPasienController
{
    private PasienRepository $pasienRepo;

    public function __construct()
    {
        $this->pasienRepo = new PasienRepository();
    }

    public function show(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $pasien = $this->pasienRepo->getById($id);

        if (!$pasien) {
            Response::json([
                'status' => 'error',
                'message' => 'Pasien tidak ditemukan'
            ], 404);
        }

        Response::json([
            'status' => 'success',
            'message' => 'OK',
            'data' => [
                'id_pasien' => (int)$pasien['id_pasien'],
                'no_rm' => $pasien['no_rm'],
                'nama' => $pasien['nama'],
                'tgl_lahir' => $pasien['tgl_lahir'],
                'jenis_kelamin' => $pasien['jenis_kelamin'],
                'alamat' => $pasien['alamat'],
                'no_hp' => $pasien['no_hp'],
                'created_at' => $pasien['created_at']
            ]
        ]);
    }
}
