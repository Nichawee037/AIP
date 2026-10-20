<?php
require_once __DIR__ . '/includes/bootstrap.php';
header('Location: ' . (!empty($_SESSION['user']) ? 'dashboard.php' : 'login.php'));
exit;
