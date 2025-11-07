<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: listar.php?err=1");
    exit;
}

$stmt = $conexion->prepare("SELECT paciente_id FROM tratamientos WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$pid = $row['paciente_id'] ?? null;

$del = $conexion->prepare("DELETE FROM tratamientos WHERE id=?");
$ok = $del->execute([$id]);

if ($pid) {
    header("Location: /historial/ficha.php?id=".$pid."#tratamientos");
    exit;
}

header("Location: listar.php?".($ok?'ok=3':'err=del'));
exit;

