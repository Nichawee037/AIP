<?php
require_once __DIR__ . '/includes/bootstrap.php';
if ($userModel->count() > 0) {
    exit('มีบัญชีผู้ใช้แล้ว เพื่อความปลอดภัยกรุณาลบไฟล์ setup.php แล้วเข้าสู่ระบบ <a href="login.php">ที่นี่</a>');
}
$userModel->create('admin', 'Admin123!', 'ผู้ดูแลระบบ', 'admin', 'admin@example.com');
$userModel->create('teacher', 'Teacher123!', 'ครูตัวอย่าง', 'teacher', 'teacher@example.com');
$userModel->create('tech', 'Tech123!', 'ช่างซ่อมตัวอย่าง', 'technician', 'tech@example.com');
echo '<h2>สร้างบัญชีตัวอย่างเรียบร้อย</h2><p>Admin: admin / Admin123!</p><p>Teacher: teacher / Teacher123!</p><p>Technician: tech / Tech123!</p><p><b>สำคัญ:</b> ลบไฟล์ setup.php ก่อนใช้งานต่อ</p><a href="login.php">ไปหน้าเข้าสู่ระบบ</a>';
