<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400); exit('CSRF');
    }

    $paciente_id     = (int)($_POST['paciente_id'] ?? 0);
    $especialista_id = (int)($_POST['especialista_id'] ?? 0);
    $fecha_cita      = !empty($_POST['fecha_cita']) ? $_POST['fecha_cita'] : date('Y-m-d');
    $prioridad       = $_POST['prioridad'] ?? null;
    $estado          = $_POST['estado'] ?? 'pendiente';

    $stmt = $conexion->prepare("INSERT INTO asignaciones
        (paciente_id, especialista_id, fecha_cita, prioridad, estado)
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$paciente_id, $especialista_id, $fecha_cita, $prioridad, $estado]);

    header("Location: listar.php?ok=1");
    exit;
}

include __DIR__.'/../templates/header.php';
?>

<div class="container py-3">
  <h3>Nueva Asignación</h3>

  <form method="POST">
    <?php csrf_field(); ?>

    <label class="form-label">Paciente (ID)</label>
    <input type="number" name="paciente_id" class="form-control" required>

    <label class="form-label mt-2">Especialista (ID)</label>
    <input type="number" name="especialista_id" class="form-control" required>

    <label class="form-label mt-2">Fecha cita</label>
    <input type="date" name="fecha_cita" class="form-control">

    <label class="form-label mt-2">Prioridad</label>
    <select name="prioridad" class="form-select">
      <option value="alta">Alta</option>
      <option value="media">Media</option>
      <option value="baja">Baja</option>
    </select>

    <label class="form-label mt-2">Estado</label>
    <select name="estado" class="form-select">
