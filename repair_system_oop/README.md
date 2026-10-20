# ระบบแจ้งซ่อมภายในโรงเรียน (PHP OOP + MySQL)

## ความสามารถ
- เข้าสู่ระบบและแยกสิทธิ์ Admin, Teacher และ Technician
- แจ้งซ่อมและดูรายการแจ้งซ่อม
- มอบหมายงานและกำหนดวันครบกำหนด (Admin)
- เปลี่ยนสถานะงานตามลำดับ pending → accepted → in_progress → completed
- แนบรูปก่อน/หลังซ่อม
- บันทึกผลการซ่อมและค่าใช้จ่าย
- Dashboard สรุปจำนวนงาน
- ใช้ PDO prepared statements, password_hash/password_verify และ CSRF token ขั้นพื้นฐาน

## วิธีติดตั้งด้วย XAMPP
1. แตก ZIP แล้วนำโฟลเดอร์ `repair_system_oop` ไปไว้ที่ `C:\xampp\htdocs\`
2. เปิด XAMPP แล้ว Start Apache และ MySQL
3. เข้า `http://localhost/phpmyadmin` แล้ว Import ไฟล์ `database.sql`
4. ตรวจสอบค่าการเชื่อมต่อใน `config/Database.php` (ค่าเริ่มต้น user=root, password ว่าง)
5. เปิด `http://localhost/repair_system_oop/setup.php` เพื่อสร้างบัญชีตัวอย่างครั้งแรก
6. เมื่อตั้งค่าบัญชีเรียบร้อย ให้ลบไฟล์ `setup.php` หรือเปลี่ยนชื่อไฟล์เพื่อป้องกันการเรียกใช้งานซ้ำ
7. เข้า `http://localhost/repair_system_oop/`

## บัญชีตัวอย่างที่ setup.php สร้าง
- Admin: `admin` / `Admin123!`
- Teacher: `teacher` / `Teacher123!`
- Technician: `tech` / `Tech123!`

ควรเปลี่ยนรหัสผ่านก่อนใช้งานจริง และไม่ควรนำระบบตัวอย่างนี้ขึ้นเซิร์ฟเวอร์จริงโดยไม่ตรวจสอบความปลอดภัยเพิ่มเติม
