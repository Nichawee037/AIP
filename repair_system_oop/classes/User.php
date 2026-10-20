<?php
declare(strict_types=1);

class User
{
    public function __construct(private PDO $db) {}

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT id, username, fullname, role, email FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function allTechnicians(): array
    {
        return $this->db->query("SELECT id, fullname FROM users WHERE role IN ('admin','technician') ORDER BY fullname")->fetchAll();
    }

    public function create(string $username, string $password, string $fullname, string $role, ?string $email = null): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (username,password,fullname,role,email) VALUES (?,?,?,?,?)');
        return $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $fullname, $role, $email]);
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, username, fullname, role, email, created_at FROM users ORDER BY id DESC')->fetchAll();
    }
}
