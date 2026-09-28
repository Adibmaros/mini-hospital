<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Repositories\ResepMasukRepository;
use App\Clients\PendaftaranClient;

class ResepController
{
    private ResepMasukRepository $resepRepo;
    private PendaftaranClient $pendaftaranClient;

    public function __construct()
    {
        $this->resepRepo = new ResepMasukRepository();
        $this->pendaftaranClient = new PendaftaranClient();
    }

    public function index(): void
    {
        $status = Request::getQueryParams()['status'] ?? null;
        $groupedResep = $this->resepRepo->getAllGrouped($status ?: null);

        // Fetch patient names from Pendaftaran app
        foreach ($groupedResep as &$group) {
            $pasien = $this->pendaftaranClient->getPasien($group['id_pasien']);
            $group['pasien_nama'] = $pasien['nama'] ?? ("Pasien #" . $group['id_pasien']);
            $group['pasien_no_rm'] = $pasien['no_rm'] ?? 'RM-UNKNOWN';
        }

        Response::render('resep/index', [
            'groupedResep' => $groupedResep,
            'statusFilter' => $status
        ], 'Resep Masuk - Sistem Farmasi');
    }

    public function show(array $params): void
    {
        $idRm = (int)($params['id_rm'] ?? 0);
        $items = $this->resepRepo->getByIdRm($idRm);

        if (empty($items)) {
            Response::redirect('/resep?error=Data+resep+tidak+ditemukan');
            return;
        }

        $idPasien = (int)$items[0]['id_pasien'];
        $pasien = $this->pendaftaranClient->getPasien($idPasien);

        Response::render('resep/detail', [
            'idRm' => $idRm,
            'pasien' => $pasien,
            'items' => $items
        ], 'Detail Resep - Sistem Farmasi');
    }

    public function updateItemStatus(array $params): void
    {
        Csrf::verify();
        $idResep = (int)($params['id_resep'] ?? 0);

        $res = $this->resepRepo->processResepSelesai($idResep);

        $item = $this->resepRepo->getByIdResep($idResep);
        $idRm = $item ? $item['id_rm'] : 0;

        if (!$res['success']) {
            Response::redirect("/resep/{$idRm}?error=" . urlencode($res['message']));
            return;
        }

        Response::redirect("/resep/{$idRm}?msg=" . urlencode($res['message']));
    }
}
