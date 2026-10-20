<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$rows = $repairModel->allForUser($_SESSION['user']);
$statusNames = ['pending'=>'รอรับเรื่อง','accepted'=>'รับเรื่องแล้ว','in_progress'=>'กำลังดำเนินการ','completed'=>'ซ่อมเสร็จ'];
$typeNames = ['electrical'=>'ไฟฟ้า','furniture'=>'เฟอร์นิเจอร์','computer'=>'คอมพิวเตอร์','aircon'=>'เครื่องปรับอากาศ','other'=>'อื่น ๆ'];
$pageTitle = 'รายการแจ้งซ่อม';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>รายการแจ้งซ่อม</h2><a class="btn btn-primary" href="repair_create.php">+ แจ้งซ่อมใหม่</a></div>
<div class="card p-3"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>เลขที่</th><th>อาคาร/ห้อง</th><th>ประเภท</th><th>ผู้แจ้ง</th><th>ผู้รับผิดชอบ</th><th>สถานะ</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr>
<td><?= e($r['request_no']) ?></td><td><?= e($r['building']) ?> / <?= e($r['room']) ?></td><td><?= e($typeNames[$r['problem_type']] ?? $r['problem_type']) ?></td>
<td><?= e($r['reporter']) ?></td><td><?= e($r['technician'] ?? 'ยังไม่มอบหมาย') ?></td>
<td><span class="badge text-bg-<?= $r['status']==='completed'?'success':($r['status']==='in_progress'?'info':($r['status']==='pending'?'warning':'primary')) ?>"><?= e($statusNames[$r['status']] ?? $r['status']) ?></span></td>
<td><a class="btn btn-sm btn-outline-primary" href="repair_detail.php?id=<?= (int)$r['id'] ?>">รายละเอียด</a></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="7" class="text-center text-muted">ยังไม่มีรายการแจ้งซ่อม</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
