<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/csrf.php';
require_once __DIR__.'/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf'] ?? '')) {
    header('Location: ' . APP_URL . 'index.php?err=1'); exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    header('Location: ' . APP_URL . 'index.php?err=1'); exit;
}

if (login($username, $password)) {
    redirect_by_role($_SESSION['user']['rol']); // ya hace exit()
} else {
    header('Location: ' . APP_URL . 'index.php?err=1'); exit;
}
?>
