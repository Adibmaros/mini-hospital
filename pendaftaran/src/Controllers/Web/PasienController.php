<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Repositories\PasienRepository;

class PasienController
{
    private PasienRepository $pasienRepo;

    public function __construct()
    {
        $this->pasienRepo = new PasienRepository();
    }

    public function index(): void
    {
        $q = Request::getQueryParams()['q'] ?? null;
        $pasienList = $this->pasienRepo->getAll($q);

        Response::render('pasien/index', [
            'pasienList' => $pasienList,
            'query' => $q
        ], 'Daftar Pasien - Sistem Pendaftaran');
    }

    public function create(): void
    {
        $nextNoRm = $this->pasienRepo->generateNextNoRm();
        Response::render('pasien/create', [
            'nextNoRm' => $nextNoRm,
            'errors' => [],
            'old' => []
        ], 'Tambah Pasien - Sistem Pendaftaran');
    }

    public function store(): void
    {
        Csrf::verify();
        $body = Request::getBody();

        $errors = [];
        if (empty($body['nama'])) {
            $errors['nama'] = 'Nama pasien wajib diisi';
        }
        if (empty($body['tgl_lahir'])) {
            $errors['tgl_lahir'] = 'Tanggal lahir wajib diisi';
        }
        if (empty($body['jenis_kelamin']) || !in_array($body['jenis_kelamin'], ['L', 'P'])) {
            $errors['jenis_kelamin'] = 'Jenis kelamin wajib dipilih (L/P)';
        }
        if (empty($body['alamat'])) {
            $errors['alamat'] = 'Alamat wajib diisi';
        }

        if (!empty($errors)) {
            Response::render('pasien/create', [
                'nextNoRm' => $body['no_rm'] ?? $this->pasienRepo->generateNextNoRm(),
                'errors' => $errors,
                'old' => $body
            ], 'Tambah Pasien - Sistem Pendaftaran');
            return;
        }

        $noRm = $this->pasienRepo->generateNextNoRm();
        $this->pasienRepo->create([
            'no_rm' => $noRm,
            'nama' => trim($body['nama']),
            'tgl_lahir' => $body['tgl_lahir'],
            'jenis_kelamin' => $body['jenis_kelamin'],
            'alamat' => trim($body['alamat']),
            'no_hp' => trim($body['no_hp'] ?? '') ?: null,
        ]);

        Response::redirect('/pasien?msg=Pasien+berhasil+didaftarkan');
    }

    public function edit(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $pasien = $this->pasienRepo->getById($id);

        if (!$pasien) {
            Response::redirect('/pasien?error=Pasien+tidak+ditemukan');
            return;
        }

        Response::render('pasien/edit', [
            'pasien' => $pasien,
            'errors' => []
        ], 'Edit Pasien - Sistem Pendaftaran');
    }

    public function update(array $params): void
    {
        Csrf::verify();
        $id = (int)($params['id'] ?? 0);
        $pasien = $this->pasienRepo->getById($id);

        if (!$pasien) {
            Response::redirect('/pasien?error=Pasien+tidak+ditemukan');
            return;
        }

        $body = Request::getBody();
        $errors = [];

        if (empty($body['nama'])) {
            $errors['nama'] = 'Nama pasien wajib diisi';
        }
        if (empty($body['tgl_lahir'])) {
            $errors['tgl_lahir'] = 'Tanggal lahir wajib diisi';
        }
        if (empty($body['jenis_kelamin']) || !in_array($body['jenis_kelamin'], ['L', 'P'])) {
            $errors['jenis_kelamin'] = 'Jenis kelamin wajib dipilih';
        }
        if (empty($body['alamat'])) {
            $errors['alamat'] = 'Alamat wajib diisi';
        }

        if (!empty($errors)) {
            Response::render('pasien/edit', [
                'pasien' => array_merge($pasien, $body),
                'errors' => $errors
            ], 'Edit Pasien - Sistem Pendaftaran');
            return;
        }

        $this->pasienRepo->update($id, [
            'nama' => trim($body['nama']),
            'tgl_lahir' => $body['tgl_lahir'],
            'jenis_kelamin' => $body['jenis_kelamin'],
            'alamat' => trim($body['alamat']),
            'no_hp' => trim($body['no_hp'] ?? '') ?: null,
        ]);

        Response::redirect('/pasien?msg=Data+pasien+berhasil+diperbarui');
    }
}
