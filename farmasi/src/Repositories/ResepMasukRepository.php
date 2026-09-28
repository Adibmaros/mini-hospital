<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ResepMasukRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByIdResep(int $idResep): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM resep_masuk WHERE id_resep = :id_resep");
        $stmt->execute(['id_resep' => $idResep]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByIdRm(int $idRm): array
    {
        $stmt = $this->db->prepare("SELECT * FROM resep_masuk WHERE id_rm = :id_rm ORDER BY id_resep_masuk ASC");
        $stmt->execute(['id_rm' => $idRm]);
        return $stmt->fetchAll();
    }

    public function getAllGrouped(?string $status = null): array
    {
        $sql = "SELECT id_rm, id_pasien, MAX(tgl_masuk) as latest_tgl
                FROM resep_masuk";
        $params = [];
        if (!empty($status)) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        $sql .= " GROUP BY id_rm, id_pasien ORDER BY latest_tgl DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $groups = $stmt->fetchAll();

        $result = [];
        foreach ($groups as $g) {
            $stmtItems = $this->db->prepare(
                "SELECT * FROM resep_masuk WHERE id_rm = :id_rm ORDER BY id_resep_masuk ASC"
            );
            $stmtItems->execute(['id_rm' => $g['id_rm']]);
            $items = $stmtItems->fetchAll();

            $result[] = [
                'id_rm' => (int)$g['id_rm'],
                'id_pasien' => (int)$g['id_pasien'],
                'tgl_masuk' => $g['latest_tgl'],
                'items' => $items
            ];
        }
        return $result;
    }

    public function createBatch(int $idRm, int $idPasien, array $items): array
    {
        $this->db->beginTransaction();
        try {
            $inserted = [];
            foreach ($items as $item) {
                // Check idempotency (id_resep already inserted?)
                $stmtCheck = $this->db->prepare("SELECT id_resep_masuk, status FROM resep_masuk WHERE id_resep = :id_resep");
                $stmtCheck->execute(['id_resep' => $item['id_resep']]);
                $existing = $stmtCheck->fetch();

                if ($existing) {
                    $this->db->rollBack();
                    return [
                        'success' => false,
                        'code' => 409,
                        'message' => "Item resep dengan id_resep {$item['id_resep']} sudah pernah diproses."
                    ];
                }

                $stmt = $this->db->prepare(
                    "INSERT INTO resep_masuk (id_resep, id_rm, id_pasien, nama_obat, dosis, jumlah, aturan_pakai, status, tgl_masuk)
                     VALUES (:id_resep, :id_rm, :id_pasien, :nama_obat, :dosis, :jumlah, :aturan_pakai, 'menunggu', NOW())"
                );
                $stmt->execute([
                    'id_resep' => $item['id_resep'],
                    'id_rm' => $idRm,
                    'id_pasien' => $idPasien,
                    'nama_obat' => $item['nama_obat'],
                    'dosis' => $item['dosis'],
                    'jumlah' => $item['jumlah'],
                    'aturan_pakai' => $item['aturan_pakai']
                ]);

                $idResepMasuk = (int)$this->db->lastInsertId();
                $inserted[] = [
                    'id_resep_masuk' => $idResepMasuk,
                    'id_resep' => (int)$item['id_resep'],
                    'status' => 'menunggu'
                ];
            }
            $this->db->commit();
            return [
                'success' => true,
                'data' => [
                    'id_rm' => $idRm,
                    'items' => $inserted
                ]
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function processResepSelesai(int $idResep): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT * FROM resep_masuk WHERE id_resep = :id_resep FOR UPDATE");
            $stmt->execute(['id_resep' => $idResep]);
            $resep = $stmt->fetch();

            if (!$resep) {
                $this->db->rollBack();
                return ['success' => false, 'code' => 404, 'message' => 'Resep tidak ditemukan'];
            }

            if ($resep['status'] === 'selesai') {
                $this->db->rollBack();
                return ['success' => false, 'code' => 409, 'message' => 'Resep sudah berstatus selesai'];
            }

            // Lock medicine row and check stock
            $stmtObat = $this->db->prepare("SELECT * FROM obat WHERE nama_obat = :nama FOR UPDATE");
            $stmtObat->execute(['nama' => $resep['nama_obat']]);
            $obat = $stmtObat->fetch();

            $jumlahDibutuhkan = (int)$resep['jumlah'];
            $stokTersedia = $obat ? (int)$obat['stok'] : 0;

            if ($stokTersedia < $jumlahDibutuhkan) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'code' => 409,
                    'message' => "Stok tidak mencukupi untuk obat [{$resep['nama_obat']}]. Stok tersedia: {$stokTersedia}, dibutuhkan: {$jumlahDibutuhkan}"
                ];
            }

            // Reduce stock
            $stmtDeduct = $this->db->prepare("UPDATE obat SET stok = stok - :jumlah WHERE id_obat = :id_obat");
            $stmtDeduct->execute([
                'jumlah' => $jumlahDibutuhkan,
                'id_obat' => $obat['id_obat']
            ]);

            // Update status resep_masuk
            $stmtUpdate = $this->db->prepare(
                "UPDATE resep_masuk SET status = 'selesai', tgl_selesai = NOW() WHERE id_resep = :id_resep"
            );
            $stmtUpdate->execute(['id_resep' => $idResep]);

            $this->db->commit();
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Status resep berhasil diubah menjadi selesai',
                'data' => [
                    'id_resep' => $idResep,
                    'status' => 'selesai',
                    'stok_sisa' => $stokTersedia - $jumlahDibutuhkan
                ]
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
