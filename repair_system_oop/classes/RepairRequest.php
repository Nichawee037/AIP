<?php
declare(strict_types=1);

class RepairRequest
{
    public function __construct(private PDO $db) {}

    public function create(int $userId, string $building, string $room, string $type, string $description, string $urgency): int
    {
        $requestNo = 'RP-' . date('Ym') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $stmt = $this->db->prepare(
            'INSERT INTO repair_requests (request_no,user_id,building,room,problem_type,description,urgency)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([$requestNo, $userId, $building, $room, $type, $description, $urgency]);
        return (int)$this->db->lastInsertId();
    }

    public function allForUser(array $user): array
    {
        if ($user['role'] === 'admin') {
            $sql = 'SELECT r.*, u.fullname AS reporter, t.fullname AS technician
                    FROM repair_requests r JOIN users u ON r.user_id=u.id
                    LEFT JOIN users t ON r.assigned_to=t.id ORDER BY r.created_at DESC';
            return $this->db->query($sql)->fetchAll();
        }
        if ($user['role'] === 'technician') {
            $stmt = $this->db->prepare('SELECT r.*, u.fullname AS reporter, t.fullname AS technician
                FROM repair_requests r JOIN users u ON r.user_id=u.id
                LEFT JOIN users t ON r.assigned_to=t.id
                WHERE r.assigned_to=? OR r.status IN (\'pending\',\'accepted\') ORDER BY r.created_at DESC');
            $stmt->execute([$user['id']]);
            return $stmt->fetchAll();
        }
        $stmt = $this->db->prepare('SELECT r.*, u.fullname AS reporter, t.fullname AS technician
            FROM repair_requests r JOIN users u ON r.user_id=u.id
            LEFT JOIN users t ON r.assigned_to=t.id WHERE r.user_id=? ORDER BY r.created_at DESC');
        $stmt->execute([$user['id']]);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT r.*, u.fullname AS reporter, t.fullname AS technician
            FROM repair_requests r JOIN users u ON r.user_id=u.id
            LEFT JOIN users t ON r.assigned_to=t.id WHERE r.id=?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function canView(array $repair, array $user): bool
    {
        return $user['role'] === 'admin'
            || (int)$repair['user_id'] === (int)$user['id']
            || ($user['role'] === 'technician');
    }

    public function assign(int $id, ?int $technicianId, ?string $dueDate): void
    {
        $stmt = $this->db->prepare("UPDATE repair_requests SET assigned_to=?, due_date=?, status=IF(status='pending','accepted',status) WHERE id=?");
        $stmt->execute([$technicianId, $dueDate ?: null, $id]);
    }

    public function updateStatus(int $id, string $status, ?float $cost = null, ?string $result = null): void
    {
        $allowed = ['accepted', 'in_progress', 'completed'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('สถานะไม่ถูกต้อง');
        }
        $repair = $this->find($id);
        if (!$repair) {
            throw new RuntimeException('ไม่พบใบแจ้งซ่อม');
        }
        $valid = [
            'pending' => ['accepted'],
            'accepted' => ['in_progress'],
            'in_progress' => ['completed'],
            'completed' => [],
        ];
        if (!in_array($status, $valid[$repair['status']] ?? [], true)) {
            throw new RuntimeException('ไม่สามารถเปลี่ยนสถานะจาก ' . $repair['status'] . ' เป็น ' . $status . ' ได้');
        }
        $completionDate = $status === 'completed' ? date('Y-m-d') : null;
        $stmt = $this->db->prepare('UPDATE repair_requests SET status=?, cost=COALESCE(?,cost), result=COALESCE(?,result), completion_date=COALESCE(?,completion_date) WHERE id=?');
        $stmt->execute([$status, $cost, $result, $completionDate, $id]);
    }

    public function stats(): array
    {
        $result = ['total'=>0,'pending'=>0,'in_progress'=>0,'completed'=>0];
        foreach ($this->db->query('SELECT status, COUNT(*) AS total FROM repair_requests GROUP BY status')->fetchAll() as $row) {
            $result['total'] += (int)$row['total'];
            if ($row['status'] === 'pending') $result['pending'] = (int)$row['total'];
            if ($row['status'] === 'in_progress') $result['in_progress'] = (int)$row['total'];
            if ($row['status'] === 'completed') $result['completed'] = (int)$row['total'];
        }
        return $result;
    }
}
