<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conexion->prepare("SELECT a.*, 
                                   p.nombre AS paciente_nombre, p.apellido AS paciente_apellido, 
                                   e.nombre AS especialista_nombre, e.especialidad
                            FROM asignaciones a
                            JOIN pacientes p ON p.id=a.paciente_id
                            JOIN especialistas e ON e.id=a.especialista_id
                            WHERE a.id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ http_response_code(404); exit('No encontrada'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprobante de Cita</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{margin:20px;background:#f8f9fa;}
.comprobante-header{background:#0d6efd;color:#fff;padding:12px 16px;border-radius:8px 8px 0 0;
display:flex;align-items:center;gap:10px;}
.comprobante-header img{width:45px;height:45px}
.comprobante-body{background:#fff;border:1px solid #dee2e6;border-top:0;border-radius:0 0 8px 8px;
padding:15px;font-size:15px;}
.badge-estado{background:#ffc107;color:#000;font-weight:bold;padding:3px 8px;border-radius:5px;}
@media print {.no-print{display:none!important} body{background:#fff!important;}}
</style>
</head>
<body>
<div class="comprobante-header">
  <img src="../assets/logo.png" alt="Logo">
  <div>
    <h4 class="m-0 fw-bold">Centro de Salud Sur de Huehuetenango</h4>
    <small>Comprobante de cita médica</small>
  </div>
  <div class="ms-auto no-print">
    <button class="btn btn-light btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
  </div>
</div>

<div class="comprobante-body">
  <p><b>Paciente:</b> <?= htmlspecialchars($row['paciente_nombre'].' '.$row['paciente_apellido']) ?></p>
  <p><b>Fecha de cita:</b> <?= htmlspecialchars($row['fecha_cita']) ?></p>
  <p><b>Prioridad:</b> <?= htmlspecialchars($row['prioridad']) ?></p>
  <p><b>Estado:</b> <span class="badge-estado"><?= htmlspecialchars($row['estado']) ?></span></p>
  <p><b>Especialista:</b> <?= htmlspecialchars($row['especialista_nombre']) ?></p>
  <p><b>Especialidad:</b> <?= htmlspecialchars($row['especialidad']) ?></p>
  <hr>
  <small>Preséntese al Centro de Salud Sur 10 minutos antes de su cita con su documento de identificación.</small>
</div>
</body>
</html>
