<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ../especialista/listar.php?err=1'); exit; }

try {
  $conn->beginTransaction();

  // 1) Borra asignaciones que dependan de este especialista (evita error de FK)
  $stmt = $conn->prepare("DELETE FROM asignaciones WHERE especialista_id = ?");
  $stmt->execute([$id]);

  // 2) Borra el especialista
  $stmt = $conn->prepare("DELETE FROM especialistas WHERE id = ?");
  $stmt->execute([$id]);

  $conn->commit();
  // ¡OJO!: la ruta correcta es con ../ porque estamos dentro de /especialista/
  header('Location: ../especialista/listar.php?ok=3');
  exit;
} catch (Throwable $e) {
  if ($conn->inTransaction()) { $conn->rollBack(); }
  header('Location: ../especialista/listar.php?err=fk');
  exit;
}
