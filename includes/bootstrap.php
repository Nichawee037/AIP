<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/RepairRequest.php';
require_once __DIR__ . '/../classes/RepairImage.php';
require_once __DIR__ . '/../classes/Comment.php';

$db = (new Database())->connect();
$userModel = new User($db);
$repairModel = new RepairRequest($db);
$imageModel = new RepairImage($db);
$commentModel = new Comment($db);

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('คำขอไม่ถูกต้อง กรุณากลับไปลองใหม่');
        }
    }
}
function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
function require_role(array $roles): void {
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        exit('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type'=>$type, 'message'=>$message];
}
