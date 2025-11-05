<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM tratamientos WHERE id=?");
$stmt->execute([$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$t){ exit("No encontrado"); }

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre,apellido")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) exit("CSRF");

    $paciente_id  = (int)($_POST['paciente_id']);
    $diagnostico  = trim($_POST['diagnostico']);
    $plan         = trim($_POST['plan']);
    $estado       = $_POST['estado'] ?? 'activo';
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin    = $_POST['fecha_fin'] ?: null;

    $up = $conexion->prepare("
        UPDATE tratamientos
        SET paciente_id=?, diagnostico=?, plan=?, estado=?, fecha_inicio=?, fecha_fin=?
        WHERE id=?
    ");
    $up->execute([$paciente_id,$diagnostico,$plan,$estado,$fecha_inicio,$fecha_fin,$id]);

    header("Location: listar.php?ok=2");
    exit;
}

include __DIR__.'/../templates/header.php';
?>
