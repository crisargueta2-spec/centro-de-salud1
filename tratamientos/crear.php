<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) { exit('CSRF'); }

    $paciente_id  = (int)($_POST['paciente_id'] ?? 0);
    $diagnostico  = trim($_POST['diagnostico'] ?? '');
    $plan         = trim($_POST['plan'] ?? '');
    $estado       = $_POST['estado'] ?? 'activo';
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin    = $_POST['fecha_fin'] ?: null;

    $stmt = $conexion->prepare("
        INSERT INTO tratamientos (paciente_id, diagnostico, plan, estado, fecha_inicio, fecha_fin)
        VALUES (?,?,?,?,?,?)
    ");
    $stmt->execute([$paciente_id,$diagnostico,$plan,$estado,$fecha_inicio,$fecha_fin]);

    header("Location: listar.php?ok=1");
    exit;
}

include __DIR__.'/../templates/header.php';
?>
