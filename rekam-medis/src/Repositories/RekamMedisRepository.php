<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class RekamMedisRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByKunjunganId(int $idKunjungan): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT rm.*, d.nama_dokter, d.spesialis
             FROM rekam_medis rm
             JOIN dokter d ON rm.id_dokter = d.id_dokter
             WHERE rm.id_kunjungan = :id_kunjungan"
        );
        $stmt->execute(['id_kunjungan' => $idKunjungan]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getById(int $idRm): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT rm.*, d.nama_dokter, d.spesialis
             FROM rekam_medis rm
             JOIN dokter d ON rm.id_dokter = d.id_dokter
             WHERE rm.id_rm = :id"
        );
        $stmt->execute(['id' => $idRm]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByPasienId(int $idPasien): array
    {
        $stmt = $this->db->prepare(
            "SELECT rm.*, d.nama_dokter, d.spesialis
             FROM rekam_medis rm
             JOIN dokter d ON rm.id_dokter = d.id_dokter
             WHERE rm.id_pasien = :id_pasien
             ORDER BY rm.tgl_periksa DESC"
        );
        $stmt->execute(['id_pasien' => $idPasien]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT rm.*, d.nama_dokter, d.spesialis
             FROM rekam_medis rm
             JOIN dokter d ON rm.id_dokter = d.id_dokter
             ORDER BY rm.tgl_periksa DESC"
        );
        return $stmt->fetchAll();
    }

    public function create(array $rmData, array $resepItems): array
    {
        $this->db->beginTransaction();
        try {
            // Rule check: 1 visit = 1 RM
            $existing = $this->getByKunjunganId((int)$rmData['id_kunjungan']);
            if ($existing) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'code' => 409,
                    'message' => 'Rekam medis untuk kunjungan ini sudah pernah dibuat (1 kunjungan = 1 rekam medis).'
                ];
            }

            $stmt = $this->db->prepare(
                "INSERT INTO rekam_medis (id_kunjungan, id_pasien, id_dokter, keluhan, diagnosa, tgl_periksa)
                 VALUES (:id_kunjungan, :id_pasien, :id_dokter, :keluhan, :diagnosa, NOW())"
            );
            $stmt->execute([
                'id_kunjungan' => $rmData['id_kunjungan'],
                'id_pasien' => $rmData['id_pasien'],
                'id_dokter' => $rmData['id_dokter'],
                'keluhan' => $rmData['keluhan'],
                'diagnosa' => $rmData['diagnosa']
            ]);

            $idRm = (int)$this->db->lastInsertId();

            $insertedResep = [];
            if (!empty($resepItems)) {
                $stmtResep = $this->db->prepare(
                    "INSERT INTO resep (id_rm, nama_obat, dosis, jumlah, aturan_pakai)
                     VALUES (:id_rm, :nama_obat, :dosis, :jumlah, :aturan_pakai)"
                );
                foreach ($resepItems as $item) {
                    $stmtResep->execute([
                        'id_rm' => $idRm,
                        'nama_obat' => $item['nama_obat'],
                        'dosis' => $item['dosis'],
                        'jumlah' => $item['jumlah'],
                        'aturan_pakai' => $item['aturan_pakai']
                    ]);
                    $idResep = (int)$this->db->lastInsertId();
                    $insertedResep[] = [
                        'id_resep' => $idResep,
                        'nama_obat' => $item['nama_obat'],
                        'dosis' => $item['dosis'],
                        'jumlah' => (int)$item['jumlah'],
                        'aturan_pakai' => $item['aturan_pakai']
                    ];
                }
            }

            $this->db->commit();
            return [
                'success' => true,
                'id_rm' => $idRm,
                'resep' => $insertedResep
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
