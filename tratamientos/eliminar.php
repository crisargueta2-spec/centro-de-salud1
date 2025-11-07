<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$base = basename(__DIR__);

$id=(int)($_GET['id']??0);
if(!$id){ header("Location: $base/listar.php?err=1"); exit; }

$stmt=$conexion->prepare("DELETE FROM tratamientos WHERE id=?");
$stmt->execute([$id]);

header("Location: $base/listar.php?ok=3");
exit;
