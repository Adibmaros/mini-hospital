<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\KunjunganRepository;

class ApiKunjunganController
{
    private KunjunganRepository $kunjunganRepo;

    public function __construct()
    {
        $this->kunjunganRepo = new KunjunganRepository();
    }

    public function index(): void
    {
        $params = Request::getQueryParams();
        $tanggal = $params['tanggal'] ?? date('Y-m-d');
        $poli = $params['poli'] ?? null;
        $status = $params['status'] ?? null;

        $rows = $this->kunjunganRepo->getAll($tanggal, $poli, $status);

        $formatted = array_map(function ($row) {
            return [
                'id_kunjungan' => (int)$row['id_kunjungan'],
                'tgl_kunjungan' => $row['tgl_kunjungan'],
                'poli' => $row['poli'],
                'no_antrean' => (int)$row['no_antrean'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'pasien' => [
                    'id_pasien' => (int)$row['id_pasien'],
                    'no_rm' => $row['no_rm'],
                    'nama' => $row['nama'],
                    'tgl_lahir' => $row['tgl_lahir'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'alamat' => $row['alamat'],
                    'no_hp' => $row['no_hp']
                ]
            ];
        }, $rows);

        Response::json([
            'status' => 'success',
            'message' => 'OK',
            'data' => $formatted
        ]);
    }

    public function show(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $row = $this->kunjunganRepo->getById($id);

        if (!$row) {
            Response::json([
                'status' => 'error',
                'message' => 'Kunjungan tidak ditemukan'
            ], 404);
        }

        Response::json([
            'status' => 'success',
            'message' => 'OK',
            'data' => [
                'id_kunjungan' => (int)$row['id_kunjungan'],
                'tgl_kunjungan' => $row['tgl_kunjungan'],
                'poli' => $row['poli'],
                'no_antrean' => (int)$row['no_antrean'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'pasien' => [
                    'id_pasien' => (int)$row['id_pasien'],
                    'no_rm' => $row['no_rm'],
                    'nama' => $row['nama'],
                    'tgl_lahir' => $row['tgl_lahir'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'alamat' => $row['alamat'],
                    'no_hp' => $row['no_hp']
                ]
            ]
        ]);
    }

    public function updateStatus(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $kunjungan = $this->kunjunganRepo->getById($id);

        if (!$kunjungan) {
            Response::json([
                'status' => 'error',
                'message' => 'Kunjungan tidak ditemukan'
            ], 404);
        }

        $body = Request::getBody();
        $newStatus = $body['status'] ?? null;

        if ($newStatus !== 'selesai') {
            Response::json([
                'status' => 'error',
                'message' => 'Status tidak valid. Hanya menerima status "selesai"'
            ], 422);
        }

        if ($kunjungan['status'] !== 'menunggu') {
            Response::json([
                'status' => 'error',
                'message' => 'Transisi status tidak valid. Hanya kunjungan berstatus "menunggu" yang dapat diubah ke "selesai"'
            ], 409);
        }

        $this->kunjunganRepo->updateStatus($id, 'selesai');

        Response::json([
            'status' => 'success',
            'message' => 'Status kunjungan berhasil diperbarui menjadi selesai',
            'data' => [
                'id_kunjungan' => $id,
                'status' => 'selesai'
            ]
        ]);
    }
}
