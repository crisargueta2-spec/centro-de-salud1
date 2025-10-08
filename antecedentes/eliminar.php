<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ header('Location: ../antecedentes/listar.php?err=1'); exit; }

$stmt = $conn->prepare("DELETE FROM antecedentes WHERE id=?");
if ($stmt->execute([$id])) {
  header('Location: ../antecedentes/listar.php?ok=3');
} else {
  header('Location: ../antecedentes/listar.php?err=del');
}
exit;
