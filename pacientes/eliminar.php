<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: listar.php?err=1'); exit; }

try {
  $conexion->beginTransaction();

  $stmt = $conexion->prepare("DELETE FROM seguimientos WHERE paciente_id = ?");
  $stmt->execute([$id]);

  $stmt = $conexion->prepare("DELETE FROM asignaciones WHERE paciente_id = ?");
  $stmt->execute([$id]);

  $stmt = $conexion->prepare("DELETE FROM pacientes WHERE id = ?");
  $stmt->execute([$id]);

  $conexion->commit();
  header('Location: listar.php?ok=3');
  exit;
} catch (Throwable $e) {
  if ($conexion->inTransaction()) { $conexion->rollBack(); }
  header('Location: listar.php?err=fk');
  exit;
}
?>


