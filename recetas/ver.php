<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$id = (int)($_GET['id'] ?? 0);
$s = $conn->prepare("SELECT r.*, p.nombre, p.apellido, u.username AS medico, t.diagnostico
                     FROM recetas r
                     JOIN pacientes p ON p.id = r.paciente_id
                     LEFT JOIN usuarios u ON u.id = r.medico_id
                     LEFT JOIN tratamientos t ON t.id = r.tratamiento_id
                     WHERE r.id=?");
$s->execute([$id]);
$rec = $s->fetch(PDO::FETCH_ASSOC);
if (!$rec) { http_response_code(404); exit('No encontrada'); }

$items = $conn->prepare("SELECT * FROM receta_items WHERE receta_id=? ORDER BY id");
$items->execute([$id]);
$lines = $items->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
@media print { .no-print{display:none!important} body{background:#fff!important;} }
.comprobante-header {
  background:#0d6efd;
  color:#fff;
  padding:10px 15px;
  border-radius:8px 8px 0 0;
  display:flex;
  align-items:center;
  gap:10px;
}
.comprobante-header img {
  width:45px; height:45px;
}
.comprobante-body {
  border:1px solid #dee2e6;
  border-top:0;
  border-radius:0 0 8px 8px;
  padding:15px;
  background:#fff;
  font-size:15px;
}
</style>

<div class="comprobante-header">
  <img src="../assets/logo.png" alt="Logo">
  <div>
    <h4 class="m-0 fw-bold">Centro de Salud Sur</h4>
    <small>Receta médica</small>
  </div>
  <div class="ms-auto no-print">
    <button class="btn btn-outline-light btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
  </div>
</div>

<div class="comprobante-body">
  <div class="row g-3">
    <div class="col-md-4"><b>Paciente:</b> <?= htmlspecialchars($rec['nombre'].' '.$rec['apellido']) ?></div>
    <div class="col-md-4"><b>Fecha:</b> <?= htmlspecialchars($rec['fecha_emision']) ?></div>
    <div class="col-md-4"><b>Médico:</b> <?= htmlspecialchars($rec['medico'] ?? '') ?></div>
    <div class="col-12"><b>Diagnóstico:</b> <?= htmlspecialchars($rec['diagnostico'] ?? '—') ?></div>
    <?php if(!empty($rec['observaciones'])): ?>
    <div class="col-12"><b>Observaciones:</b> <?= nl2br(htmlspecialchars($rec['observaciones'])) ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="table-card mt-3">
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Medicamento</th><th>Presentación</th><th>Dosis</th>
          <th>Frecuencia</th><th>Vía</th><th>Duración</th><th>Indicaciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($lines as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['medicamento']) ?></td>
            <td><?= htmlspecialchars($it['presentacion']) ?></td>
            <td><?= htmlspecialchars($it['dosis']) ?></td>
            <td><?= htmlspecialchars($it['frecuencia']) ?></td>
            <td><?= htmlspecialchars($it['via']) ?></td>
            <td><?= htmlspecialchars($it['duracion']) ?></td>
            <td><?= nl2br(htmlspecialchars($it['indicaciones'])) ?></td>
          </tr>
        <?php endforeach; if (empty($lines)): ?>
          <tr><td colspan="7" class="text-center text-muted">Sin renglones</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
