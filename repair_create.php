<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $building = trim($_POST['building'] ?? '');
    $room = trim($_POST['room'] ?? '');
    $type = $_POST['problem_type'] ?? 'other';
    $description = trim($_POST['description'] ?? '');
    $urgency = $_POST['urgency'] ?? 'medium';
    if ($building === '' || $room === '' || $description === '') $errors[] = 'กรุณากรอกอาคาร ห้อง และรายละเอียดปัญหา';
    if (!in_array($type, ['electrical','furniture','computer','aircon','other'], true)) $errors[] = 'ประเภทปัญหาไม่ถูกต้อง';
    if (!in_array($urgency, ['low','medium','high'], true)) $errors[] = 'ระดับความเร่งด่วนไม่ถูกต้อง';
    if (!$errors) {
        $id = $repairModel->create((int)$_SESSION['user']['id'],$building,$room,$type,$description,$urgency);
        flash('success','แจ้งซ่อมเรียบร้อยแล้ว');
        header('Location: repair_detail.php?id='.$id); exit;
    }
}
$pageTitle = 'แจ้งซ่อมใหม่';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card p-4">
<h2 class="mb-3">แจ้งซ่อมใหม่</h2>
<?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="row g-3">
<div class="col-md-6"><label class="form-label">อาคาร</label><input class="form-control" name="building" required value="<?= e($_POST['building'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">ห้อง</label><input class="form-control" name="room" required value="<?= e($_POST['room'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">ประเภทปัญหา</label><select class="form-select" name="problem_type">
<option value="electrical">ไฟฟ้า</option><option value="furniture">เฟอร์นิเจอร์</option><option value="computer">คอมพิวเตอร์</option><option value="aircon">เครื่องปรับอากาศ</option><option value="other" selected>อื่น ๆ</option>
</select></div>
<div class="col-md-6"><label class="form-label">ความเร่งด่วน</label><select class="form-select" name="urgency"><option value="low">ต่ำ</option><option value="medium" selected>ปานกลาง</option><option value="high">สูง</option></select></div>
<div class="col-12"><label class="form-label">รายละเอียดปัญหา</label><textarea class="form-control" name="description" rows="4" required><?= e($_POST['description'] ?? '') ?></textarea></div>
</div><button class="btn btn-primary mt-3">ส่งใบแจ้งซ่อม</button>
</form></div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
