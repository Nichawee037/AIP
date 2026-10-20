<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$stats = $repairModel->stats();
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<h2 class="mb-4">Dashboard</h2>
<div class="row g-3 mb-4">
<?php foreach ([['งานทั้งหมด',$stats['total'],'primary'],['รอดำเนินการ',$stats['pending'],'warning'],['กำลังดำเนินการ',$stats['in_progress'],'info'],['ซ่อมเสร็จแล้ว',$stats['completed'],'success']] as [$label,$num,$color]): ?>
<div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted"><?= e($label) ?></div><div class="display-6 text-<?= e($color) ?>"><?= (int)$num ?></div></div></div>
<?php endforeach; ?>
</div>
<div class="card p-4"><h5>เริ่มต้นใช้งาน</h5><p>เลือกเมนูด้านบนเพื่อแจ้งซ่อม ตรวจสอบสถานะ หรือดูรายละเอียดใบแจ้งซ่อม</p><a class="btn btn-primary align-self-start" href="repair_create.php">+ แจ้งซ่อมใหม่</a></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
