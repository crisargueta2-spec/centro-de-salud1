<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$paciente_id     = (int)($_GET['paciente_id'] ?? 0);
$tratamiento_id  = (int)($_GET['tratamiento_id'] ?? 0);
$user            = user();
$medico_id       = (int)($user['id'] ?? 0);

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre,apellido")->fetchAll(PDO::FETCH_ASSOC);
$trats = [];
if ($paciente_id) {
  $st = $conexion->prepare("SELECT id, diagnostico, estado FROM tratamientos WHERE paciente_id=? ORDER BY id DESC");
  $st->execute([$paciente_id]);
  $trats = $st->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if(!csrf_validate($_POST['csrf_token'] ?? '')){ http_response_code(400); exit('CSRF'); }

  $paciente_id    = (int)($_POST['paciente_id'] ?? 0);
  $tratamiento_id = (int)($_POST['tratamiento_id'] ?? 0) ?: null;
  $fecha_emision  = $_POST['fecha_emision'] ?: date('Y-m-d');
  $obs            = trim($_POST['observaciones'] ?? '');

  $ins = $conexion->prepare("INSERT INTO recetas (paciente_id,tratamiento_id,medico_id,fecha_emision,observaciones)
                             VALUES (?,?,?,?,?)");
  $ins->execute([$paciente_id,$tratamiento_id,$medico_id,$fecha_emision,$obs]);
  $rid = (int)$conexion->lastInsertId();

  $medicamento = $_POST['medicamento'] ?? [];
  $presentacion= $_POST['presentacion'] ?? [];
  $dosis       = $_POST['dosis'] ?? [];
  $frecuencia  = $_POST['frecuencia'] ?? [];
  $via         = $_POST['via'] ?? [];
  $duracion    = $_POST['duracion'] ?? [];
  $indic       = $_POST['indicaciones'] ?? [];

  $it = $conexion->prepare("INSERT INTO receta_items (receta_id, medicamento, presentacion, dosis, frecuencia, via, duracion, indicaciones)
                            VALUES (?,?,?,?,?,?,?,?)");
  for ($i=0; $i<count($medicamento); $i++) {
    $m = trim($medicamento[$i] ?? '');
    if ($m === '') continue;
    $it->execute([
      $rid,
      $m,
      trim($presentacion[$i] ?? ''),
      trim($dosis[$i] ?? ''),
      trim($frecuencia[$i] ?? ''),
      trim($via[$i] ?? ''),
      trim($duracion[$i] ?? ''),
      trim($indic[$i] ?? '')
    ]);
  }

  header('Location: ../recetas/ver.php?id='.$rid);
  exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>
.form-page{display:flex;justify-content:center}
.form-card{max-width:1000px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}
.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
.form-card-body{padding:16px}
</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Emitir receta</div>
    <div class="form-card-body">
      <form method="POST" action="../recetas/crear.php" class="row g-3">
        <?php csrf_field(); ?>

        <div class="col-md-6">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required onchange="location='../recetas/crear.php?paciente_id='+this.value">
            <option value="">— Seleccione —</option>
            <?php foreach($pacientes as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $p['id']==$paciente_id?'selected':'' ?>>
                <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Fecha de emisión</label>
          <input type="date" name="fecha_emision" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label">Tratamiento (opcional)</label>
          <select name="tratamiento_id" class="form-select">
            <option value="">— Sin asociar —</option>
            <?php foreach($trats as $t): ?>
              <option value="<?= $t['id'] ?>" <?= $t['id']==$tratamiento_id?'selected':'' ?>>
                <?= htmlspecialchars(($t['diagnostico']?:'Tratamiento')." — ".$t['estado']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Observaciones (opcional)</label>
          <textarea name="observaciones" class="form-control" rows="2"></textarea>
        </div>

        <div class="col-12">
          <hr><h5 class="mb-2">Medicamentos</h5>
          <p class="text-muted small">Deja en blanco las filas que no uses.</p>
          <?php for($i=0;$i<6;$i++): ?>
            <div class="row g-2 align-items-end mb-1">
              <div class="col-lg-3"><label class="form-label">Medicamento</label><input class="form-control" name="medicamento[]"></div>
              <div class="col-lg-2"><label class="form-label">Presentación</label><input class="form-control" name="presentacion[]"></div>
              <div class="col-lg-2"><label class="form-label">Dosis</label><input class="form-control" name="dosis[]"></div>
              <div class="col-lg-2"><label class="form-label">Frecuencia</label><input class="form-control" name="frecuencia[]"></div>
              <div class="col-lg-1"><label class="form-label">Vía</label><input class="form-control" name="via[]"></div>
              <div class="col-lg-2"><label class="form-label">Duración</label><input class="form-control" name="duracion[]"></div>
              <div class="col-12"><label class="form-label">Indicaciones</label><input class="form-control" name="indicaciones[]"></div>
            </div>
            <?php if($i<5): ?><hr class="text-muted"><?php endif; ?>
          <?php endfor; ?>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="../recetas/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar receta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
