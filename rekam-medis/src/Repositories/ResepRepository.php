<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ResepRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByRmId(int $idRm): array
    {
        $stmt = $this->db->prepare("SELECT * FROM resep WHERE id_rm = :id_rm ORDER BY id_resep ASC");
        $stmt->execute(['id_rm' => $idRm]);
        return $stmt->fetchAll();
    }

    public function getById(int $idResep): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM resep WHERE id_resep = :id");
        $stmt->execute(['id' => $idResep]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
