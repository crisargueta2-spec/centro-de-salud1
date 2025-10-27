<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']); // cualquiera puede imprimir
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ http_response_code(400); exit('Falta ID'); }

$sql = "SELECT s.id, s.fecha_registro, s.resultado, s.proxima_cita,
               p.nombre, p.apellido, p.genero, p.fecha_nacimiento
        FROM seguimientos s
        JOIN pacientes p ON p.id = s.paciente_id
        WHERE s.id=?";
$st = $conn->prepare($sql);
$st->execute([$id]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if(!$row){ http_response_code(404); exit('Seguimiento no encontrado'); }

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
  <h2 class="mb-0">Constancia de seguimiento</h2>
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
      <div>Constancia de atención / seguimiento</div>
    </div>
  </div>
  <div class="body">
    <div class="pair mb-2">
      <div><b>Paciente:</b> <?= htmlspecialchars($row['nombre'].' '.$row['apellido']) ?></div>
      <div><b>Género/Edad:</b> <?= htmlspecialchars(trim(($row['genero'] ?? '').' / '.$edad, ' /')) ?></div>
    </div>
    <div class="pair mb-2">
      <div><b>Fecha de atención:</b> <?= htmlspecialchars($row['fecha_registro']) ?></div>
      <div><b>Próxima cita:</b> <?= htmlspecialchars($row['proxima_cita'] ?? '—') ?></div>
    </div>
    <div class="mb-2">
      <b>Resumen / Indicaciones:</b>
      <div><?= nl2br(htmlspecialchars($row['resultado'])) ?></div>
    </div>
    <hr>
    <div class="hint">
      Este documento es informativo para el paciente. Conservar para su control.  
      En caso de dudas, acudir al establecimiento o llamar a su número de contacto.
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
