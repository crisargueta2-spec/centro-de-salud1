<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: listar.php?err=1");
    exit;
}

// obtener paciente para volver a su ficha
$stmt = $conexion->prepare("SELECT paciente_id FROM tratamientos WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$paciente_id = $row['paciente_id'] ?? null;

$del = $conexion->prepare("DELETE FROM tratamientos WHERE id=?");
$ok  = $del->execute([$id]);

if ($paciente_id) {
    header("Location: ../historial/ficha.php?id={$paciente_id}#tratamientos");
    exit;
}

header("Location: listar.php?".($ok ? "ok=3" : "err=del"));
exit;
