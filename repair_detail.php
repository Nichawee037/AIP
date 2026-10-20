<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$repair = $repairModel->find($id);
if (!$repair || !$repairModel->canView($repair, $_SESSION['user'])) { http_response_code(404); exit('ไม่พบรายการ หรือไม่มีสิทธิ์ดูรายการนี้'); }
$images = $imageModel->forRepair($id);
$comments = $commentModel->forRepair($id);
$techs = $userModel->allTechnicians();
$types = ['electrical'=>'ไฟฟ้า','furniture'=>'เฟอร์นิเจอร์','computer'=>'คอมพิวเตอร์','aircon'=>'เครื่องปรับอากาศ','other'=>'อื่น ๆ'];
$statuses = ['pending'=>'รอรับเรื่อง','accepted'=>'รับเรื่องแล้ว','in_progress'=>'กำลังดำเนินการ','completed'=>'ซ่อมเสร็จ'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'assign') {
            require_role(['admin']);
            $techId = ($_POST['assigned_to'] ?? '') !== '' ? (int)$_POST['assigned_to'] : null;
            $due = trim($_POST['due_date'] ?? '');
            $repairModel->assign($id,$techId,$due ?: null);
            flash('success','มอบหมายงานเรียบร้อย');
        } elseif ($action === 'status') {
            require_role(['admin','technician']);
            $cost = trim($_POST['cost'] ?? '');
            $result = trim($_POST['result'] ?? '');
            $repairModel->updateStatus($id,$_POST['status'] ?? '',$cost === '' ? null : (float)$cost,$result === '' ? null : $result);
            flash('success','เปลี่ยนสถานะเรียบร้อย');
        } elseif ($action === 'comment') {
            $comment = trim($_POST['comment'] ?? '');
            if ($comment !== '') $commentModel->add($id,(int)$_SESSION['user']['id'],$comment);
            flash('success','เพิ่มความคิดเห็นแล้ว');
        }
    } catch (Throwable $e) { flash('error',$e->getMessage()); }
    header('Location: repair_detail.php?id='.$id); exit;
}
$pageTitle = 'รายละเอียดใบแจ้งซ่อม';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>รายละเอียดใบแจ้งซ่อม</h2><a class="btn btn-outline-secondary" href="repairs.php">กลับรายการ</a></div>
<div class="card p-4 mb-3">
<div class="row g-3">
<div class="col-md-6"><strong>เลขที่ใบแจ้งซ่อม:</strong> <?= e($repair['request_no']) ?></div>
<div class="col-md-6"><strong>สถานะ:</strong> <?= e($statuses[$repair['status']] ?? $repair['status']) ?></div>
<div class="col-md-6"><strong>อาคาร/ห้อง:</strong> <?= e($repair['building']) ?> / <?= e($repair['room']) ?></div>
<div class="col-md-6"><strong>ประเภท:</strong> <?= e($types[$repair['problem_type']] ?? $repair['problem_type']) ?></div>
<div class="col-md-6"><strong>ผู้แจ้ง:</strong> <?= e($repair['reporter']) ?></div>
<div class="col-md-6"><strong>ผู้รับผิดชอบ:</strong> <?= e($repair['technician'] ?? 'ยังไม่มอบหมาย') ?></div>
<div class="col-12"><strong>รายละเอียด:</strong><p class="mb-0"><?= nl2br(e($repair['description'])) ?></p></div>
<div class="col-md-6"><strong>วันครบกำหนด:</strong> <?= e($repair['due_date'] ?? '-') ?></div>
<div class="col-md-6"><strong>ค่าใช้จ่าย:</strong> <?= e($repair['cost'] === null ? '-' : number_format((float)$repair['cost'],2)) ?></div>
<div class="col-12"><strong>ผลการซ่อม:</strong><p><?= nl2br(e($repair['result'] ?? '-')) ?></p></div>
</div></div>

<?php if ($_SESSION['user']['role']==='admin'): ?>
<div class="card p-4 mb-3"><h5>มอบหมายงาน (Admin)</h5><form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="assign">
<div class="col-md-6"><label class="form-label">ผู้รับผิดชอบ</label><select class="form-select" name="assigned_to"><option value="">ยังไม่มอบหมาย</option><?php foreach($techs as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)$repair['assigned_to']===(int)$t['id']?'selected':'' ?>><?= e($t['fullname']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">วันครบกำหนด</label><input class="form-control" type="date" name="due_date" value="<?= e($repair['due_date'] ?? '') ?>"></div>
<div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">บันทึก</button></div></form></div>
<?php endif; ?>

<?php if (in_array($_SESSION['user']['role'],['admin','technician'],true) && $repair['status']!=='completed'): ?>
<div class="card p-4 mb-3"><h5>อัปเดตสถานะงาน</h5><form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="status">
<div class="col-md-4"><label class="form-label">สถานะถัดไป</label><select class="form-select" name="status" required>
<?php $next = ['pending'=>['accepted'=>'รับเรื่องแล้ว'],'accepted'=>['in_progress'=>'กำลังดำเนินการ'],'in_progress'=>['completed'=>'ซ่อมเสร็จ']]; foreach(($next[$repair['status']] ?? []) as $k=>$v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?>
</select></div>
<div class="col-md-3"><label class="form-label">ค่าใช้จ่าย (ถ้ามี)</label><input class="form-control" type="number" min="0" step="0.01" name="cost" value="<?= e($repair['cost'] === null ? '' : (string)$repair['cost']) ?>"></div>
<div class="col-md-5"><label class="form-label">ผลการซ่อม</label><input class="form-control" name="result" value="<?= e($repair['result'] ?? '') ?>"></div>
<div class="col-12"><button class="btn btn-success">บันทึกสถานะ</button></div></form></div>
<?php endif; ?>

<div class="card p-4 mb-3"><h5>แนบรูปก่อน/หลังซ่อม</h5>
<form action="upload_image.php" method="post" enctype="multipart/form-data" class="row g-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="repair_id" value="<?= $id ?>">
<div class="col-md-4"><select class="form-select" name="image_type"><option value="before">ก่อนซ่อม</option><option value="after">หลังซ่อม</option></select></div>
<div class="col-md-6"><input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp" required></div>
<div class="col-md-2"><button class="btn btn-outline-primary w-100">อัปโหลด</button></div></form>
<div class="row g-3 mt-2"><?php foreach($images as $img): ?><div class="col-6 col-md-3"><div class="border rounded p-2"><img src="<?= e($img['image_path']) ?>" class="img-fluid rounded" alt="รูปประกอบ"><div class="small mt-1"><?= $img['image_type']==='before'?'ก่อนซ่อม':'หลังซ่อม' ?></div></div></div><?php endforeach; ?></div>
</div>

<div class="card p-4"><h5>ความคิดเห็น</h5>
<?php foreach($comments as $c): ?><div class="border-bottom py-2"><strong><?= e($c['fullname']) ?></strong> <small class="text-muted"><?= e($c['created_at']) ?></small><div><?= nl2br(e($c['comment'])) ?></div></div><?php endforeach; ?>
<form method="post" class="mt-3"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="comment"><textarea class="form-control mb-2" name="comment" rows="2" required placeholder="เขียนความคิดเห็น..."></textarea><button class="btn btn-primary">ส่งความคิดเห็น</button></form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
