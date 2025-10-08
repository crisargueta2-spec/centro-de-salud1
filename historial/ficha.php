<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$id = (int)($_GET['id'] ?? 0);
if(!$id){ http_response_code(400); exit('Falta ID'); }

// Datos del paciente
$p = $conn->prepare("SELECT * FROM pacientes WHERE id=?");
$p->execute([$id]);
$pac = $p->fetch(PDO::FETCH_ASSOC);
if(!$pac){ http_response_code(404); exit('Paciente no encontrado'); }

// Asignaciones
$asig = $conn->prepare("
  SELECT a.fecha_cita, a.prioridad, e.nombre AS especialista, e.especialidad
  FROM asignaciones a
  JOIN especialistas e ON e.id = a.especialista_id
  WHERE a.paciente_id = ?
  ORDER BY a.fecha_cita DESC, a.id DESC
");
$asig->execute([$id]);
$asignaciones = $asig->fetchAll(PDO::FETCH_ASSOC);

// Seguimientos
$segs = $conn->prepare("
  SELECT resultado, proxima_cita, fecha_registro
  FROM seguimientos WHERE paciente_id = ?
  ORDER BY fecha_registro DESC, id DESC
");
$segs->execute([$id]);
$seguimientos = $segs->fetchAll(PDO::FETCH_ASSOC);

// Antecedentes (si existe tabla)
try {
  $ante = $conn->prepare("
    SELECT tipo, descripcion, fecha_registro
    FROM antecedentes WHERE paciente_id = ?
    ORDER BY fecha_registro DESC, id DESC
  ");
  $ante->execute([$id]);
  $antecedentes = $ante->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $antecedentes = [];
}

// Tratamientos (si existe tabla)
try {
  $trat = $conn->prepare("
    SELECT diagnostico, COALESCE(plan, tratamiento) AS plan_texto, estado, fecha_inicio, fecha_fin
    FROM tratamientos WHERE paciente_id = ?
    ORDER BY COALESCE(fecha_inicio, id) DESC
  ");
  $trat->execute([$id]);
  $tratamientos = $trat->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $tratamientos = [];
}

// Recetas del paciente
try {
  $rc = $conn->prepare("SELECT r.id, r.fecha_emision, r.observaciones,
                               u.username AS medico, t.diagnostico
                        FROM recetas r
                        LEFT JOIN usuarios u ON u.id = r.medico_id
                        LEFT JOIN tratamientos t ON t.id = r.tratamiento_id
                        WHERE r.paciente_id = ?
                        ORDER BY r.fecha_emision DESC, r.id DESC");
  $rc->execute([$id]);
  $recetas = $rc->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $recetas = [];
}

// Rol actual (para botones de receta)
$rol = strtolower(user()['rol'] ?? (user()['role'] ?? ''));
?>
<style>
@media print { .no-print{display:none!important} }
</style>

<div class="topbar">
  <h2 class="mb-0">Ficha clínica — <?= htmlspecialchars($pac['nombre'].' '.$pac['apellido']) ?></h2>
  <div class="d-flex gap-2 no-print">
    <a href="historial/listar.php" class="btn btn-secondary">Volver</a>
    <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-bold">Datos del paciente</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><b>Nombre:</b> <?= htmlspecialchars($pac['nombre'].' '.$pac['apellido']) ?></div>
      <div class="col-md-4"><b>Género:</b> <?= htmlspecialchars($pac['genero']) ?></div>
      <div class="col-md-4"><b>Fecha nacimiento:</b> <?= htmlspecialchars($pac['fecha_nacimiento']) ?></div>
      <div class="col-md-4"><b>Ingreso (referencia):</b> <?= htmlspecialchars($pac['fecha_referencia']) ?></div>
      <div class="col-md-8"><b>Motivo / Síntomas:</b> <?= nl2br(htmlspecialchars($pac['motivo'])) ?></div>
      <div class="col-md-6"><b>Médico referente:</b> <?= htmlspecialchars($pac['medico_referente']) ?></div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-bold">Asignaciones</div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr><th>Fecha cita</th><th>Prioridad</th><th>Especialista</th></tr>
      </thead>
      <tbody>
        <?php foreach($asignaciones as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['fecha_cita']) ?></td>
            <td><?= htmlspecialchars($a['prioridad'] ?? '—') ?></td>
            <td><?= htmlspecialchars($a['especialista']).' — '.htmlspecialchars($a['especialidad']) ?></td>
          </tr>
        <?php endforeach; if(empty($asignaciones)): ?>
          <tr><td colspan="3" class="text-center text-muted">Sin asignaciones</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-bold">Seguimientos</div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr><th>Fecha</th><th>Resultado</th><th>Próxima cita</th></tr>
      </thead>
      <tbody>
        <?php foreach($seguimientos as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['fecha_registro']) ?></td>
            <td><?= nl2br(htmlspecialchars($s['resultado'])) ?></td>
            <td><?= htmlspecialchars($s['proxima_cita']) ?></td>
          </tr>
        <?php endforeach; if(empty($seguimientos)): ?>
          <tr><td colspan="3" class="text-center text-muted">Sin seguimientos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-bold">Antecedentes</div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr><th>Fecha</th><th>Tipo</th><th>Descripción</th></tr>
      </thead>
      <tbody>
        <?php foreach($antecedentes as $an): ?>
          <tr>
            <td><?= htmlspecialchars($an['fecha_registro']) ?></td>
            <td><span class="badge text-bg-secondary"><?= htmlspecialchars($an['tipo']) ?></span></td>
            <td><?= nl2br(htmlspecialchars($an['descripcion'])) ?></td>
          </tr>
        <?php endforeach; if(empty($antecedentes)): ?>
          <tr><td colspan="3" class="text-center text-muted">Sin antecedentes registrados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header fw-bold">Tratamientos</div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr><th>Diagnóstico</th><th>Plan</th><th>Estado</th><th>Inicio</th><th>Fin</th></tr>
      </thead>
      <tbody>
        <?php foreach($tratamientos as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t['diagnostico']) ?></td>
            <td><?= nl2br(htmlspecialchars($t['plan_texto'] ?? '')) ?></td>
            <td><span class="badge text-bg-<?= $t['estado']==='activo'?'success':($t['estado']==='suspendido'?'warning':'secondary') ?>"><?= htmlspecialchars($t['estado']) ?></span></td>
            <td><?= htmlspecialchars($t['fecha_inicio']) ?></td>
            <td><?= htmlspecialchars($t['fecha_fin']) ?></td>
          </tr>
        <?php endforeach; if(empty($tratamientos)): ?>
          <tr><td colspan="5" class="text-center text-muted">Sin tratamientos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<a id="recetas"></a>
<div class="card shadow-sm mb-4">
  <div class="card-header fw-bold">Recetas</div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Fecha</th><th>Médico</th><th>Diagnóstico</th><th>Observaciones</th><th class="no-print" style="width:150px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($recetas as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fecha_emision']) ?></td>
            <td><?= htmlspecialchars($r['medico'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['diagnostico'] ?? '') ?></td>
            <td><?= nl2br(htmlspecialchars($r['observaciones'] ?? '')) ?></td>
            <td class="no-print">
              <a class="btn btn-sm btn-outline-primary" href="recetas/ver.php?id=<?= $r['id'] ?>">Ver / Imprimir</a>
            </td>
          </tr>
        <?php endforeach; if(empty($recetas)): ?>
          <tr><td colspan="5" class="text-center text-muted">Sin recetas</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <?php if (in_array($rol, ['admin','medico'])): ?>
      <div class="mt-2 no-print">
        <a class="btn btn-primary" href="recetas/crear.php?paciente_id=<?= $id ?>">
          <i class="bi bi-file-medical"></i> Emitir receta
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
