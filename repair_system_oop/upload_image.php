<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: repairs.php'); exit; }
verify_csrf();
$id = filter_input(INPUT_POST, 'repair_id', FILTER_VALIDATE_INT) ?: 0;
$repair = $repairModel->find($id);
if (!$repair || !$repairModel->canView($repair, $_SESSION['user'])) { http_response_code(403); exit('ไม่มีสิทธิ์'); }
$type = $_POST['image_type'] ?? '';
if (!in_array($type,['before','after'],true) || empty($_FILES['image']['tmp_name'])) {
    flash('error','กรุณาเลือกไฟล์ภาพให้ถูกต้อง'); header('Location: repair_detail.php?id='.$id); exit;
}
$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5*1024*1024) {
    flash('error','ไฟล์มีปัญหาหรือมีขนาดเกิน 5 MB'); header('Location: repair_detail.php?id='.$id); exit;
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
if (!isset($allowed[$mime])) {
    flash('error','อนุญาตเฉพาะ JPG, PNG, GIF และ WEBP'); header('Location: repair_detail.php?id='.$id); exit;
}
$dir = __DIR__ . '/uploads';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
$dest = $dir . '/' . $name;
if (!move_uploaded_file($file['tmp_name'], $dest)) {
    flash('error','อัปโหลดไฟล์ไม่สำเร็จ'); header('Location: repair_detail.php?id='.$id); exit;
}
$imageModel->add($id,(int)$_SESSION['user']['id'],$type,'uploads/'.$name);
flash('success','อัปโหลดรูปภาพแล้ว');
header('Location: repair_detail.php?id='.$id); exit;
