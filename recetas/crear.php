<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$paciente_id = (int)($_GET['paciente_id'] ?? 0);

// Pacientes
$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

// Tratamientos del paciente (si aplica)
$tratamientos = [];
if ($paciente_id) {
    $stmt = $conexion->prepare("SELECT id, diagnostico FROM tratamientos WHERE paciente_id = ?");
    $stmt->execute([$paciente_id]);
    $tratamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit('CSRF');
    }

    $paciente_id = (int)($_POST['paciente_id'] ?? 0);
    $tratamiento_id = !empty($_POST['tratamiento_id']) ? (int)$_POST['tratamiento_id'] : null;
    $medico_id = user()['id'] ?? null;
    $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d');
    $observaciones = trim($_POST['observaciones'] ?? '');

    $sql = "INSERT INTO recetas (paciente_id, tratamiento_id, medico_id, fecha_emision, observaciones)
            VALUES (?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$paciente_id, $tratamiento_id, $medico_id, $fecha_emision, $observaciones]);

    header('Location: ../historial/ficha.php?id=' . $paciente_id . '&ok=receta');
    exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>
.form-page {display:flex;justify-content:center}
.form-card {max-width:720px;width:100%;background:#fff;border-radius:10px;
  box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}
.form-card-head {background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
.form-card-body {padding:16px}
</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Emitir nueva receta</div>
    <div class="form-card-body">
      <form method="POST" action="../recetas/crear.php" class="row g-3">
        <?php csrf_field(); ?>

        <div class="col-12">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required>
            <option value="">— Seleccione —</option>
            <?php foreach($pacientes as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $p['id']==$paciente_id?'selected':'' ?>>
                <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Tratamiento asociado (opcional)</label>
          <select name="tratamiento_id" class="form-select">
            <option value="">— Ninguno —</option>
            <?php foreach($tratamientos as $t): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['diagnostico']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha emisión</label>
          <input type="date" name="fecha_emision" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="4" placeholder="Indicaciones o comentarios..." required></textarea>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="../historial/ficha.php?id=<?= $paciente_id ?>" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar receta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
