<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Repositories\ObatRepository;

class ObatController
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

        Response::render('obat/index', [
            'obats' => $obats,
            'query' => $q
        ], 'Master Obat - Sistem Farmasi');
    }

    public function create(): void
    {
        Response::render('obat/create', [
            'errors' => [],
            'old' => []
        ], 'Tambah Obat - Sistem Farmasi');
    }

    public function store(): void
    {
        Csrf::verify();
        $body = Request::getBody();

        $errors = [];
        if (empty($body['nama_obat'])) {
            $errors['nama_obat'] = 'Nama obat wajib diisi';
        } else {
            $existing = $this->obatRepo->getByName(trim($body['nama_obat']));
            if ($existing) {
                $errors['nama_obat'] = 'Nama obat sudah ada dalam master';
            }
        }

        if (!isset($body['stok']) || $body['stok'] === '' || (int)$body['stok'] < 0) {
            $errors['stok'] = 'Stok wajib diisi angka >= 0';
        }
        if (!isset($body['harga']) || $body['harga'] === '' || (float)$body['harga'] < 0) {
            $errors['harga'] = 'Harga wajib diisi angka >= 0';
        }

        if (!empty($errors)) {
            Response::render('obat/create', [
                'errors' => $errors,
                'old' => $body
            ], 'Tambah Obat - Sistem Farmasi');
            return;
        }

        $this->obatRepo->create([
            'nama_obat' => trim($body['nama_obat']),
            'stok' => (int)$body['stok'],
            'harga' => (float)$body['harga']
        ]);

        Response::redirect('/obat?msg=Obat+berhasil+ditambahkan');
    }

    public function edit(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $obat = $this->obatRepo->getById($id);

        if (!$obat) {
            Response::redirect('/obat?error=Obat+tidak+ditemukan');
            return;
        }

        Response::render('obat/edit', [
            'obat' => $obat,
            'errors' => []
        ], 'Edit Obat - Sistem Farmasi');
    }

    public function update(array $params): void
    {
        Csrf::verify();
        $id = (int)($params['id'] ?? 0);
        $obat = $this->obatRepo->getById($id);

        if (!$obat) {
            Response::redirect('/obat?error=Obat+tidak+ditemukan');
            return;
        }

        $body = Request::getBody();
        $errors = [];

        if (empty($body['nama_obat'])) {
            $errors['nama_obat'] = 'Nama obat wajib diisi';
        }
        if (!isset($body['stok']) || $body['stok'] === '' || (int)$body['stok'] < 0) {
            $errors['stok'] = 'Stok wajib diisi angka >= 0';
        }
        if (!isset($body['harga']) || $body['harga'] === '' || (float)$body['harga'] < 0) {
            $errors['harga'] = 'Harga wajib diisi angka >= 0';
        }

        if (!empty($errors)) {
            Response::render('obat/edit', [
                'obat' => array_merge($obat, $body),
                'errors' => $errors
            ], 'Edit Obat - Sistem Farmasi');
            return;
        }

        $this->obatRepo->update($id, [
            'nama_obat' => trim($body['nama_obat']),
            'stok' => (int)$body['stok'],
            'harga' => (float)$body['harga']
        ]);

        Response::redirect('/obat?msg=Data+obat+berhasil+diperbarui');
    }
}
