<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: listar.php?msg=deleted');
exit;

