<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PasienRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $query = null): array
    {
        if ($query) {
            $stmt = $this->db->prepare(
                "SELECT * FROM pasien WHERE nama LIKE :q OR no_rm LIKE :q ORDER BY id_pasien DESC"
            );
            $stmt->execute(['q' => '%' . $query . '%']);
        } else {
            $stmt = $this->db->query("SELECT * FROM pasien ORDER BY id_pasien DESC");
        }
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pasien WHERE id_pasien = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByNoRm(string $noRm): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pasien WHERE no_rm = :no_rm");
        $stmt->execute(['no_rm' => $noRm]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function generateNextNoRm(): string
    {
        $stmt = $this->db->query("SELECT MAX(id_pasien) AS max_id FROM pasien");
        $row = $stmt->fetch();
        $nextId = ((int)($row['max_id'] ?? 0)) + 1;
        return sprintf("RM-%06d", $nextId);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pasien (no_rm, nama, tgl_lahir, jenis_kelamin, alamat, no_hp)
             VALUES (:no_rm, :nama, :tgl_lahir, :jenis_kelamin, :alamat, :no_hp)"
        );
        $stmt->execute([
            'no_rm' => $data['no_rm'],
            'nama' => $data['nama'],
            'tgl_lahir' => $data['tgl_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'],
            'no_hp' => $data['no_hp'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE pasien SET nama = :nama, tgl_lahir = :tgl_lahir, jenis_kelamin = :jenis_kelamin,
             alamat = :alamat, no_hp = :no_hp WHERE id_pasien = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'nama' => $data['nama'],
            'tgl_lahir' => $data['tgl_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'],
            'no_hp' => $data['no_hp'] ?? null,
        ]);
    }
}
