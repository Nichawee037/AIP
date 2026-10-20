<?php
declare(strict_types=1);

class RepairImage
{
    public function __construct(private PDO $db) {}

    public function add(int $repairId, int $userId, string $type, string $path): void
    {
        $stmt = $this->db->prepare('INSERT INTO repair_images (repair_id,uploaded_by,image_type,image_path) VALUES (?,?,?,?)');
        $stmt->execute([$repairId, $userId, $type, $path]);
    }

    public function forRepair(int $repairId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_images WHERE repair_id=? ORDER BY uploaded_at DESC');
        $stmt->execute([$repairId]);
        return $stmt->fetchAll();
    }
}
