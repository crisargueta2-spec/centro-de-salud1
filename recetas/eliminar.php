<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ header('Location: ../recetas/listar.php?err=1'); exit; }

$stmt = $conn->prepare("DELETE FROM recetas WHERE id=?");
$stmt->execute([$id]);

$conn->prepare("DELETE FROM receta_items WHERE receta_id=?")->execute([$id]);

header('Location: ../recetas/listar.php?ok=3');
exit;
?>
