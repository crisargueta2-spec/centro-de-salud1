<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if($id){
    $stmt = $conn->prepare("DELETE FROM seguimientos WHERE id=?");
    $stmt->execute([$id]);
}

header("Location: /seguimientos/listar.php?ok=3");
exit;
