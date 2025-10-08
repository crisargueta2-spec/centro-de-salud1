<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf'] ?? '')) {
  http_response_code(400);
  exit('Solicitud inválida.');
}

$id = (int)($_POST['id'] ?? 0);
$nuevoEstado = $_POST['estado'] ?? '';

$permitidos = ['pendiente','atendido','cancelado'];
if ($id <= 0 || !in_array($nuevoEstado, $permitidos, true)) {
  http_response_code(422);
  exit('Parámetros inválidos.');
}

// (Opcional) limitar transiciones; aquí permitimos a cualquiera de los 3 estados.
$stmt = $conn->prepare("UPDATE asignaciones SET estado = ? WHERE id = ?");
$stmt->execute([$nuevoEstado, $id]);

// Redirige de vuelta manteniendo filtros si venían en la URL
$back = $_POST['back'] ?? '/asignaciones/listar.php?ok=1';
header("Location: $back");
exit;
