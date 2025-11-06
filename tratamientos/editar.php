<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM tratamientos WHERE id=?");
$stmt->execute([$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$t) {
    http_response_code(404);
    exit("No encontrado");
}

$pacientes = $conexion->query("SELECT id, nombre, apellido 
                               FROM pacientes 
                               ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_validate($_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit("CSRF");
    }

    $paciente_id  = (int)($_POST['paciente_id'] ?? 0);
    $diagnostico  = trim($_POST['diagnostico'] ?? '');
    $plan         = trim($_POST['plan'] ?? '');
    $estado       = $_POST['estado'] ?? 'activo';
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin    = $_POST['fecha_fin'] ?: null;

    $up = $conexion->prepare("
        UPDATE tratamientos
        SET paciente_id=?, diagnostico=?, plan=?, estado=?, fecha_inicio=?, fecha_fin=?
        WHERE id=?
    ");

    $up->execute([
        $paciente_id,
        $diagnostico,
        $plan,
        $estado,
        $fecha_inicio,
        $fecha_fin,
        $id
    ]);

    header("Location: ../historial/ficha.php?id=".$paciente_id."#tratamientos");
    exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>
.form-page{display:flex;justify-content:center}
.form-card{max-width:800px;width:100%;background:#fff;border-radius:10px;
           box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}
.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
.form-card-body{padding:16px}
</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Editar tratamiento</div>
    <div class="form-card-body">

      <form method="POST" action="editar.php?id=<?= $t['id'] ?>" class="row g-3">
        <?php csrf_field(); ?>

        <div class="col-md-6">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required>
            <?php foreach($pacientes as $p): ?>
              <option value="<?= $p['id'] ?>" 
                <?= $p['id']==$t['paciente_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <?php foreach(['activo','suspendido','finalizado'] as $st): ?>
              <option value="<?= $st ?>" <?= $t['estado']===$st ? 'selected' : '' ?>>
                <?= ucfirst($st) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Diagnóstico</label>
          <input type="text" name="diagnostico" class="form-control"
                 value="<?= htmlspecialchars($t['diagnostico']) ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Plan / Indicaciones</label>
          <textarea name="plan" class="form-control" rows="4" required><?= 
              htmlspecialchars($t['plan']) 
          ?></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Inicio</label>
          <input type="date" name="fecha_inicio" class="form-control"
                 value="<?= htmlspecialchars($t['fecha_inicio']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Fin (opcional)</label>
          <input type="date" name="fecha_fin" class="form-control"
                 value="<?= htmlspecialchars($t['fecha_fin']) ?>">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="../historial/ficha.php?id=<?= $t['paciente_id'] ?>#tratamientos" 
             class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary">Actualizar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
