<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ../pacientes/listar.php?err=1'); exit; }

try {
  $conn->beginTransaction();

  $stmt = $conn->prepare("DELETE FROM seguimientos WHERE paciente_id = ?");
  $stmt->execute([$id]);

  $stmt = $conn->prepare("DELETE FROM asignaciones WHERE paciente_id = ?");
  $stmt->execute([$id]);

  $stmt = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
  $stmt->execute([$id]);

  $conn->commit();
  header('Location: ../pacientes/listar.php?ok=3');
  exit;
} catch (Throwable $e) {
  if ($conn->inTransaction()) { $conn->rollBack(); }
  header('Location: ../pacientes/listar.php?err=fk');
  exit;
}
?>
