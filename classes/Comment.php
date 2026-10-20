<?php
declare(strict_types=1);

class Comment
{
    public function __construct(private PDO $db) {}

    public function add(int $repairId, int $userId, string $comment): void
    {
        $stmt = $this->db->prepare('INSERT INTO comments (repair_id,user_id,comment) VALUES (?,?,?)');
        $stmt->execute([$repairId, $userId, $comment]);
    }

    public function forRepair(int $repairId): array
    {
        $stmt = $this->db->prepare('SELECT c.*, u.fullname FROM comments c JOIN users u ON c.user_id=u.id WHERE c.repair_id=? ORDER BY c.created_at');
        $stmt->execute([$repairId]);
        return $stmt->fetchAll();
    }
}
