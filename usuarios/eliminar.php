<?php
require_once __DIR__.'/../includes/auth.php';
require_role('admin');
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../templates/back.php';

$id = (int)($_GET['id'] ?? 0);

// No permitir auto-eliminarse
if ($id === (int)user()['id']) {
  header('Location: ' . safe_back_url('usuarios/listar.php')); exit;
}

// No eliminar último admin
$stmt = $conn->prepare("SELECT id, role FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row && $row['role'] === 'admin') {
  $admins = (int)$conn->query("SELECT COUNT(*) FROM usuarios WHERE role='admin'")->fetchColumn();
  if ($admins <= 1) {
    header('Location: ' . safe_back_url('usuarios/listar.php')); exit;
  }
}

if ($id > 0) {
  $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
  $stmt->execute([$id]);
}

header('Location: ' . safe_back_url('usuarios/listar.php')); 
exit;
