<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Clients\PendaftaranClient;
use App\Clients\FarmasiClient;
use App\Repositories\DokterRepository;
use App\Repositories\RekamMedisRepository;
use App\Repositories\ResepRepository;

class PemeriksaanController
{
    private PendaftaranClient $pendaftaranClient;
    private FarmasiClient $farmasiClient;
    private DokterRepository $dokterRepo;
    private RekamMedisRepository $rmRepo;
    private ResepRepository $resepRepo;

    public function __construct()
    {
        $this->pendaftaranClient = new PendaftaranClient();
        $this->farmasiClient = new FarmasiClient();
        $this->dokterRepo = new DokterRepository();
        $this->rmRepo = new RekamMedisRepository();
        $this->resepRepo = new ResepRepository();
    }

    public function create(array $params): void
    {
        $idKunjungan = (int)($params['id_kunjungan'] ?? 0);

        // Check if RM already created for this kunjungan (1 visit = 1 RM)
        $existingRm = $this->rmRepo->getByKunjunganId($idKunjungan);
        if ($existingRm) {
            Response::redirect("/riwayat/{$existingRm['id_pasien']}?msg=Pemeriksaan+untuk+kunjungan+ini+sudah+pernah+dibuat");
            return;
        }

        // Fetch visit data from Pendaftaran API
        $kunjunganRes = $this->pendaftaranClient->getKunjunganById($idKunjungan);
        if (!$kunjunganRes['success'] || empty($kunjunganRes['data'])) {
            Response::render('pemeriksaan/error', [
                'message' => 'Gagal mengambil data kunjungan dari Sistem Pendaftaran. ' . $kunjunganRes['message']
            ], 'Error - Rekam Medis');
            return;
        }

        $kunjungan = $kunjunganRes['data'];
        $dokterList = $this->dokterRepo->getAll();

        // Fetch medicine list from Farmasi API (A5)
        $farmasiRes = $this->farmasiClient->getObatMaster();
        $obatMaster = $farmasiRes['success'] ? ($farmasiRes['data'] ?? []) : [];

        Response::render('pemeriksaan/form', [
            'kunjungan' => $kunjungan,
            'pasien' => $kunjungan['pasien'],
            'dokterList' => $dokterList,
            'obatMaster' => $obatMaster,
            'farmasiOnline' => $farmasiRes['success'],
            'errors' => []
        ], 'Form Pemeriksaan Pasien - Rekam Medis');
    }

    public function store(): void
    {
        Csrf::verify();
        $body = Request::getBody();

        $idKunjungan = (int)($body['id_kunjungan'] ?? 0);
        $idPasien = (int)($body['id_pasien'] ?? 0);
        $idDokter = (int)($body['id_dokter'] ?? 0);
        $keluhan = trim($body['keluhan'] ?? '');
        $diagnosa = trim($body['diagnosa'] ?? '');

        // Parse prescription items
        $obatNames = $body['obat_nama'] ?? [];
        $doses = $body['obat_dosis'] ?? [];
        $jumlahs = $body['obat_jumlah'] ?? [];
        $aturans = $body['obat_aturan'] ?? [];

        $resepItems = [];
        for ($i = 0; $i < count($obatNames); $i++) {
            $namaObat = trim($obatNames[$i] ?? '');
            $jumlah = (int)($jumlahs[$i] ?? 0);
            if (!empty($namaObat) && $jumlah > 0) {
                $resepItems[] = [
                    'nama_obat' => $namaObat,
                    'dosis' => trim($doses[$i] ?? ''),
                    'jumlah' => $jumlah,
                    'aturan_pakai' => trim($aturans[$i] ?? '1x1')
                ];
            }
        }

        // Check if RM already created (E9)
        $existingRm = $this->rmRepo->getByKunjunganId($idKunjungan);
        if ($existingRm) {
            http_response_code(409);
            Response::render('pemeriksaan/error', [
                'message' => 'Konflik (409): Rekam medis untuk kunjungan ini sudah pernah disimpan. Satu kunjungan hanya boleh memiliki satu rekam medis.'
            ], '409 Conflict - Rekam Medis');
            return;
        }

        // Create RM locally
        $createRes = $this->rmRepo->create([
            'id_kunjungan' => $idKunjungan,
            'id_pasien' => $idPasien,
            'id_dokter' => $idDokter,
            'keluhan' => $keluhan,
            'diagnosa' => $diagnosa
        ], $resepItems);

        if (!$createRes['success']) {
            http_response_code($createRes['code'] ?? 400);
            Response::render('pemeriksaan/error', [
                'message' => $createRes['message']
            ], 'Error - Rekam Medis');
            return;
        }

        $idRm = $createRes['id_rm'];
        $insertedResep = $createRes['resep'];

        // Send prescription to Farmasi API if there are items
        $farmasiStatusMsg = '';
        if (!empty($insertedResep)) {
            $payload = [
                'id_rm' => $idRm,
                'id_pasien' => $idPasien,
                'items' => $insertedResep
            ];

            $farmasiRes = $this->farmasiClient->sendResep($payload);
            if (!$farmasiRes['success']) {
                $farmasiStatusMsg = ' (Resep gagal terkirim otomatis ke Farmasi. Anda dapat menekan tombol Kirim Ulang pada riwayat RM)';
            }
        }

        // Update visit status to 'selesai' in Pendaftaran API (A6)
        $this->pendaftaranClient->updateKunjunganStatus($idKunjungan, 'selesai');

        Response::redirect("/riwayat/{$idPasien}?msg=" . urlencode("Pemeriksaan medis berhasil disimpan!" . $farmasiStatusMsg));
    }

    public function resendResep(array $params): void
    {
        Csrf::verify();
        $idRm = (int)($params['id_rm'] ?? 0);
        $rm = $this->rmRepo->getById($idRm);

        if (!$rm) {
            Response::redirect('/antrean?error=Rekam+medis+tidak+ditemukan');
            return;
        }

        $resepList = $this->resepRepo->getByRmId($idRm);
        if (empty($resepList)) {
            Response::redirect("/riwayat/{$rm['id_pasien']}?error=Tidak+ada+resep+pada+rekam+medis+ini");
            return;
        }

        $payload = [
            'id_rm' => $idRm,
            'id_pasien' => (int)$rm['id_pasien'],
            'items' => array_map(function ($r) {
                return [
                    'id_resep' => (int)$r['id_resep'],
                    'nama_obat' => $r['nama_obat'],
                    'dosis' => $r['dosis'],
                    'jumlah' => (int)$r['jumlah'],
                    'aturan_pakai' => $r['aturan_pakai']
                ];
            }, $resepList)
        ];

        $farmasiRes = $this->farmasiClient->sendResep($payload);

        // If code is 409 (already exists in farmasi), treat as success (idempotent retry)
        if ($farmasiRes['success'] || $farmasiRes['code'] === 409) {
            Response::redirect("/riwayat/{$rm['id_pasien']}?msg=Resep+berhasil+dikirim+ke+Farmasi");
        } else {
            Response::redirect("/riwayat/{$rm['id_pasien']}?error=" . urlencode("Gagal mengirim resep ke Farmasi: " . $farmasiRes['message']));
        }
    }
}
