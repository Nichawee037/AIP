<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $found = $userModel->findByUsername($username);
    if ($found && password_verify($password, $found['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$found['id'],'username'=>$found['username'],'fullname'=>$found['fullname'],'role'=>$found['role']];
        header('Location: dashboard.php'); exit;
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
$pageTitle = 'เข้าสู่ระบบ';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4">
<h3 class="mb-3">เข้าสู่ระบบ</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">ชื่อผู้ใช้</label><input class="form-control" name="username" required autocomplete="username"></div>
<div class="mb-3"><label class="form-label">รหัสผ่าน</label><input class="form-control" type="password" name="password" required autocomplete="current-password"></div>
<button class="btn btn-primary w-100">เข้าสู่ระบบ</button>
</form><p class="text-muted small mt-3 mb-0">เริ่มต้นใช้งานครั้งแรกให้เปิด setup.php ตามคู่มือ</p>
</div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
