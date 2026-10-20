<?php $pageTitle = $pageTitle ?? 'ระบบแจ้งซ่อมภายในโรงเรียน'; ?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{background:#f4f6fb}.navbar-brand{font-weight:700}.card{border:0;box-shadow:0 4px 16px #24324a12}.status{font-size:.85rem}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
<div class="container"><a class="navbar-brand" href="dashboard.php">🔧 ระบบแจ้งซ่อม</a>
<div class="d-flex gap-2 align-items-center text-white flex-wrap">
<?php if (!empty($_SESSION['user'])): ?>
<a class="btn btn-sm btn-outline-light" href="dashboard.php">Dashboard</a>
<a class="btn btn-sm btn-outline-light" href="repairs.php">รายการแจ้งซ่อม</a>
<a class="btn btn-sm btn-light" href="repair_create.php">แจ้งซ่อมใหม่</a>
<span class="small"><?= e($_SESSION['user']['fullname']) ?> (<?= e($_SESSION['user']['role']) ?>)</span>
<a class="btn btn-sm btn-warning" href="logout.php">ออกจากระบบ</a>
<?php endif; ?>
</div></div></nav>
<main class="container pb-5">
