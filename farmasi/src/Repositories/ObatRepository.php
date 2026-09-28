<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ObatRepository
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
                "SELECT * FROM obat WHERE nama_obat LIKE :q ORDER BY id_obat ASC"
            );
            $stmt->execute(['q' => '%' . $query . '%']);
        } else {
            $stmt = $this->db->query("SELECT * FROM obat ORDER BY id_obat ASC");
        }
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM obat WHERE id_obat = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByName(string $namaObat): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM obat WHERE nama_obat = :nama");
        $stmt->execute(['nama' => $namaObat]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO obat (nama_obat, stok, harga) VALUES (:nama_obat, :stok, :harga)"
        );
        $stmt->execute([
            'nama_obat' => $data['nama_obat'],
            'stok' => (int)($data['stok'] ?? 0),
            'harga' => (float)($data['harga'] ?? 0)
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE obat SET nama_obat = :nama_obat, stok = :stok, harga = :harga WHERE id_obat = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'nama_obat' => $data['nama_obat'],
            'stok' => (int)$data['stok'],
            'harga' => (float)$data['harga']
        ]);
    }
}
