<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']); // todos pueden imprimir
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ http_response_code(400); exit('Falta ID'); }

$sql = "SELECT a.id, a.fecha_cita, a.prioridad, a.estado,
               p.nombre, p.apellido, p.genero, p.fecha_nacimiento,
               e.nombre AS especialista, e.especialidad
        FROM asignaciones a
        JOIN pacientes p ON p.id = a.paciente_id
        JOIN especialistas e ON e.id = a.especialista_id
        WHERE a.id=?";
$st = $conn->prepare($sql);
$st->execute([$id]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if(!$row){ http_response_code(404); exit('Asignación no encontrada'); }

// Edad (opcional)
$edad = '';
if (!empty($row['fecha_nacimiento'])) {
  try {
    $d1 = new DateTime($row['fecha_nacimiento']);
    $d2 = new DateTime('today');
    $edad = $d1->diff($d2)->y.' años';
  } catch(Throwable $e){}
}
?>
<style>
.ticket {max-width: 720px; margin: 0 auto; background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.1)}
.ticket .head {background:#0d6efd; color:#fff; padding:16px; display:flex; align-items:center; gap:12px; border-radius:10px 10px 0 0}
.ticket .body {padding:16px}
.ticket h3{margin:0; font-weight:800}
.ticket .pair{display:flex; gap:12px; flex-wrap:wrap}
.ticket .pair > div{flex:1; min-width:220px}
.ticket .hint{font-size:.9rem; color:#6c757d}
@media print{
  .no-print{display:none!important}
  .sidebar,.toggle-btn{display:none!important}
  .content{padding:0!important}
  .ticket{box-shadow:none!important; border:0}
}
</style>

<div class="topbar">
  <h2 class="mb-0">Comprobante de cita</h2>
  <div class="d-flex gap-2 no-print">
    <a class="btn btn-secondary" href="javascript:history.back()">Volver</a>
    <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
  </div>
</div>

<div class="ticket">
  <div class="head">
    <img src="img/logo.png" alt="logo" style="width:42px;height:42px;object-fit:contain" onerror="this.style.display='none'">
    <div>
      <h3>Centro de Salud Sur</h3>
      <div>Comprobante de cita</div>
    </div>
  </div>
  <div class="body">
    <div class="pair mb-2">
      <div><b>Paciente:</b> <?= htmlspecialchars($row['nombre'].' '.$row['apellido']) ?></div>
      <div><b>Género/Edad:</b> <?= htmlspecialchars(trim(($row['genero'] ?? '').' / '.$edad, ' /')) ?></div>
    </div>
    <div class="pair mb-2">
      <div><b>Fecha de cita:</b> <?= htmlspecialchars($row['fecha_cita']) ?></div>
      <div><b>Prioridad:</b> <?= htmlspecialchars($row['prioridad'] ?? '—') ?></div>
      <div><b>Estado:</b> <span class="badge text-bg-<?= ($row['estado']??'pendiente')==='pendiente'?'warning':'secondary' ?>"><?= htmlspecialchars($row['estado'] ?? 'pendiente') ?></span></div>
    </div>
    <div class="pair mb-2">
      <div><b>Especialista:</b> <?= htmlspecialchars($row['especialista']) ?></div>
      <div><b>Especialidad:</b> <?= htmlspecialchars($row['especialidad']) ?></div>
    </div>
    <hr>
    <div class="hint">
      Por favor, presentarse 10 minutos antes de su cita con su documento de identificación.
      Si no puede asistir, comuníquese para reprogramar.
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
