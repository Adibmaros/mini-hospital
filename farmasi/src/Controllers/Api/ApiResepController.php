<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\ResepMasukRepository;
use App\Repositories\ObatRepository;

class ApiResepController
{
    private ResepMasukRepository $resepRepo;
    private ObatRepository $obatRepo;

    public function __construct()
    {
        $this->resepRepo = new ResepMasukRepository();
        $this->obatRepo = new ObatRepository();
    }

    public function store(): void
    {
        $body = Request::getBody();

        $idRm = (int)($body['id_rm'] ?? 0);
        $idPasien = (int)($body['id_pasien'] ?? 0);
        $items = $body['items'] ?? [];

        if (!$idRm || !$idPasien || empty($items) || !is_array($items)) {
            Response::json([
                'status' => 'error',
                'message' => 'Data resep tidak valid. [id_rm], [id_pasien], dan array [items] wajib diisi.'
            ], 422);
        }

        // Validate items and check master medicine (A5)
        foreach ($items as $index => $item) {
            if (empty($item['id_resep']) || empty($item['nama_obat']) || empty($item['jumlah']) || (int)$item['jumlah'] <= 0) {
                Response::json([
                    'status' => 'error',
                    'message' => "Item resep index #{$index} tidak valid (id_resep, nama_obat, jumlah > 0 wajib diisi)."
                ], 422);
            }

            // A5: Check if medicine exists in master
            $obat = $this->obatRepo->getByName($item['nama_obat']);
            if (!$obat) {
                Response::json([
                    'status' => 'error',
                    'message' => "Obat [{$item['nama_obat']}] tidak terdaftar di master obat Farmasi.",
                    'errors' => ['nama_obat' => "Obat '{$item['nama_obat']}' tidak ditemukan"]
                ], 422);
            }
        }

        $res = $this->resepRepo->createBatch($idRm, $idPasien, $items);

        if (!$res['success']) {
            Response::json([
                'status' => 'error',
                'message' => $res['message']
            ], $res['code'] ?? 409);
        }

        Response::json([
            'status' => 'success',
            'message' => 'Resep diterima',
            'data' => $res['data']
        ], 201);
    }

    public function index(): void
    {
        $params = Request::getQueryParams();
        $idRm = isset($params['id_rm']) ? (int)$params['id_rm'] : null;

        if ($idRm !== null) {
            $items = $this->resepRepo->getByIdRm($idRm);
            $formatted = array_map(function ($item) {
                return [
                    'id_resep_masuk' => (int)$item['id_resep_masuk'],
                    'id_resep' => (int)$item['id_resep'],
                    'id_rm' => (int)$item['id_rm'],
                    'id_pasien' => (int)$item['id_pasien'],
                    'nama_obat' => $item['nama_obat'],
                    'dosis' => $item['dosis'],
                    'jumlah' => (int)$item['jumlah'],
                    'aturan_pakai' => $item['aturan_pakai'],
                    'status' => $item['status'],
                    'tgl_masuk' => $item['tgl_masuk'],
                    'tgl_selesai' => $item['tgl_selesai']
                ];
            }, $items);

            Response::json([
                'status' => 'success',
                'message' => 'OK',
                'data' => $formatted
            ]);
        }

        Response::json([
            'status' => 'error',
            'message' => 'Parameter id_rm diperlukan'
        ], 400);
    }

    public function show(array $params): void
    {
        $idResep = (int)($params['id'] ?? 0);
        $item = $this->resepRepo->getByIdResep($idResep);

        if (!$item) {
            Response::json([
                'status' => 'error',
                'message' => 'Resep tidak ditemukan'
            ], 404);
        }

        Response::json([
            'status' => 'success',
            'message' => 'OK',
            'data' => [
                'id_resep_masuk' => (int)$item['id_resep_masuk'],
                'id_resep' => (int)$item['id_resep'],
                'id_rm' => (int)$item['id_rm'],
                'id_pasien' => (int)$item['id_pasien'],
                'nama_obat' => $item['nama_obat'],
                'dosis' => $item['dosis'],
                'jumlah' => (int)$item['jumlah'],
                'aturan_pakai' => $item['aturan_pakai'],
                'status' => $item['status'],
                'tgl_masuk' => $item['tgl_masuk'],
                'tgl_selesai' => $item['tgl_selesai']
            ]
        ]);
    }

    public function updateStatus(array $params): void
    {
        $idResep = (int)($params['id'] ?? 0);
        $body = Request::getBody();

        $newStatus = $body['status'] ?? null;
        if ($newStatus !== 'selesai') {
            Response::json([
                'status' => 'error',
                'message' => 'Status tidak valid. Hanya menerima status "selesai"'
            ], 422);
        }

        $res = $this->resepRepo->processResepSelesai($idResep);

        if (!$res['success']) {
            Response::json([
                'status' => 'error',
                'message' => $res['message']
            ], $res['code'] ?? 409);
        }

        Response::json([
            'status' => 'success',
            'message' => $res['message'],
            'data' => $res['data']
        ]);
    }
}
