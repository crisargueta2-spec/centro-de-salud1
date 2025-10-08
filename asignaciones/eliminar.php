<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ../asignaciones/listar.php?err=1'); exit; }

$stmt = $conn->prepare("DELETE FROM asignaciones WHERE id = ?");
if ($stmt->execute([$id])) {
  header('Location: ../asignaciones/listar.php?ok=3');   // ruta correcta
} else {
  header('Location: ../asignaciones/listar.php?err=del'); // ruta correcta
}
exit;
