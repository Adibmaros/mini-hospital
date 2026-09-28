<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class KunjunganRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $tanggal = null, ?string $poli = null, ?string $status = null): array
    {
        $sql = "SELECT k.*, p.no_rm, p.nama, p.tgl_lahir, p.jenis_kelamin, p.alamat, p.no_hp
                FROM kunjungan k
                JOIN pasien p ON k.id_pasien = p.id_pasien
                WHERE 1=1";
        $params = [];

        if (!empty($tanggal)) {
            $sql .= " AND k.tgl_kunjungan = :tanggal";
            $params['tanggal'] = $tanggal;
        }
        if (!empty($poli)) {
            $sql .= " AND k.poli = :poli";
            $params['poli'] = $poli;
        }
        if (!empty($status)) {
            $sql .= " AND k.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY k.tgl_kunjungan DESC, k.no_antrean ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT k.*, p.no_rm, p.nama, p.tgl_lahir, p.jenis_kelamin, p.alamat, p.no_hp
                FROM kunjungan k
                JOIN pasien p ON k.id_pasien = p.id_pasien
                WHERE k.id_kunjungan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function hasPendingKunjungan(int $idPasien, string $tanggal, string $poli): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM kunjungan
             WHERE id_pasien = :id_pasien AND tgl_kunjungan = :tgl AND poli = :poli AND status = 'menunggu'"
        );
        $stmt->execute([
            'id_pasien' => $idPasien,
            'tgl' => $tanggal,
            'poli' => $poli
        ]);
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0) > 0;
    }

    public function getNextNoAntrean(string $tanggal, string $poli): int
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(no_antrean) as max_antrean FROM kunjungan WHERE tgl_kunjungan = :tgl AND poli = :poli FOR UPDATE"
        );
        $stmt->execute(['tgl' => $tanggal, 'poli' => $poli]);
        $row = $stmt->fetch();
        return ((int)($row['max_antrean'] ?? 0)) + 1;
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $noAntrean = $this->getNextNoAntrean($data['tgl_kunjungan'], $data['poli']);
            $stmt = $this->db->prepare(
                "INSERT INTO kunjungan (id_pasien, tgl_kunjungan, poli, no_antrean, status)
                 VALUES (:id_pasien, :tgl_kunjungan, :poli, :no_antrean, :status)"
            );
            $stmt->execute([
                'id_pasien' => $data['id_pasien'],
                'tgl_kunjungan' => $data['tgl_kunjungan'],
                'poli' => $data['poli'],
                'no_antrean' => $noAntrean,
                'status' => $data['status'] ?? 'menunggu'
            ]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $idKunjungan, string $newStatus): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE kunjungan SET status = :status WHERE id_kunjungan = :id"
        );
        return $stmt->execute([
            'status' => $newStatus,
            'id' => $idKunjungan
        ]);
    }
}
