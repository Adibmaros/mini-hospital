<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Clients\PendaftaranClient;
use App\Clients\FarmasiClient;
use App\Repositories\RekamMedisRepository;
use App\Repositories\ResepRepository;

class RiwayatController
{
    private PendaftaranClient $pendaftaranClient;
    private FarmasiClient $farmasiClient;
    private RekamMedisRepository $rmRepo;
    private ResepRepository $resepRepo;

    public function __construct()
    {
        $this->pendaftaranClient = new PendaftaranClient();
        $this->farmasiClient = new FarmasiClient();
        $this->rmRepo = new RekamMedisRepository();
        $this->resepRepo = new ResepRepository();
    }

    public function index(): void
    {
        $allRm = $this->rmRepo->getAll();

        // Unique patients
        $patientMap = [];
        foreach ($allRm as $rm) {
            $idPasien = (int)$rm['id_pasien'];
            if (!isset($patientMap[$idPasien])) {
                $pasienRes = $this->pendaftaranClient->getPasien($idPasien);
                $patientMap[$idPasien] = [
                    'id_pasien' => $idPasien,
                    'pasien' => $pasienRes['success'] ? $pasienRes['data'] : ['nama' => "Pasien #{$idPasien}", 'no_rm' => 'RM-UNKNOWN'],
                    'count_rm' => 0
                ];
            }
            $patientMap[$idPasien]['count_rm']++;
        }

        Response::render('riwayat/index', [
            'patientList' => array_values($patientMap)
        ], 'Daftar Riwayat Rekam Medis Pasien');
    }

    public function show(array $params): void
    {
        $idPasien = (int)($params['id_pasien'] ?? 0);
        $pasienRes = $this->pendaftaranClient->getPasien($idPasien);

        $pasienData = $pasienRes['success'] ? $pasienRes['data'] : [
            'id_pasien' => $idPasien,
            'no_rm' => 'RM-UNKNOWN',
            'nama' => "Pasien #{$idPasien}",
            'tgl_lahir' => '-',
            'jenis_kelamin' => '-',
            'alamat' => '-'
        ];

        $rmList = $this->rmRepo->getByPasienId($idPasien);

        // Fetch prescription statuses from Farmasi for each RM
        foreach ($rmList as &$rm) {
            $items = $this->resepRepo->getByRmId((int)$rm['id_rm']);

            // Query Farmasi for prescription status
            $farmasiRes = $this->farmasiClient->getResepByRm((int)$rm['id_rm']);
            $farmasiMap = [];
            if ($farmasiRes['success'] && is_array($farmasiRes['data'])) {
                foreach ($farmasiRes['data'] as $fItem) {
                    $farmasiMap[(int)$fItem['id_resep']] = $fItem['status'];
                }
            }

            foreach ($items as &$it) {
                $idResep = (int)$it['id_resep'];
                if (isset($farmasiMap[$idResep])) {
                    $it['status_farmasi'] = $farmasiMap[$idResep];
                } else {
                    // Try single check or mark as 'belum_terkirim'
                    $singleRes = $this->farmasiClient->getResepStatus($idResep);
                    if ($singleRes['success'] && isset($singleRes['data']['status'])) {
                        $it['status_farmasi'] = $singleRes['data']['status'];
                    } else {
                        $it['status_farmasi'] = 'belum_terkirim';
                    }
                }
            }

            $rm['resep_items'] = $items;
        }

        Response::render('riwayat/detail', [
            'pasien' => $pasienData,
            'rmList' => $rmList
        ], "Riwayat Rekam Medis - {$pasienData['nama']}");
    }
}
