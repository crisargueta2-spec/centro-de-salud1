<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID inválido'); }

$conexion->prepare("DELETE FROM receta_items WHERE receta_id=?")->execute([$id]);
$conexion->prepare("DELETE FROM recetas WHERE id=?")->execute([$id]);

header('Location: /recetas/listar.php?ok=deleted');
exit;
?>
