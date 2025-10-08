<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$paciente_id    = (int)($_GET['paciente_id'] ?? 0);
$seguimiento_id = (int)($_GET['seguimiento_id'] ?? 0);
$medico_id      = (int)(user()['id'] ?? 0);

$pacientes = $conn->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if(!csrf_validate($_POST['csrf'] ?? '')){ http_response_code(400); exit('CSRF'); }

  $paciente_id    = (int)($_POST['paciente_id'] ?? 0);
  $diagnostico    = trim($_POST['diagnostico'] ?? '');
  $plan           = trim($_POST['plan'] ?? '');
  $estado         = $_POST['estado'] ?? 'activo';
  $fecha_inicio   = $_POST['fecha_inicio'] ?: null;
  $fecha_fin      = $_POST['fecha_fin'] ?: null;
  $seguimiento_id = (int)($_POST['seguimiento_id'] ?? 0) ?: null;

  $stmt = $conn->prepare("INSERT INTO tratamientos (paciente_id, seguimiento_id, medico_id, diagnostico, plan, estado, fecha_inicio, fecha_fin)
                          VALUES (?,?,?,?,?,?,?,?)");
  $stmt->execute([$paciente_id, $seguimiento_id, $medico_id, $diagnostico, $plan, $estado, $fecha_inicio, $fecha_fin]);

  header('Location: ../historial/ficha.php?id='.$paciente_id.'#tratamientos'); exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>.form-page{display:flex;justify-content:center}.form-card{max-width:800px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}.form-card-body{padding:16px}</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Nuevo tratamiento</div>
    <div class="form-card-body">
      <form method="POST" action="tratamientos/crear.php" class="row g-3">
        <?php csrf_field(); ?>
        <input type="hidden" name="seguimiento_id" value="<?= $seguimiento_id ?>">

        <div class="col-md-6">
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

        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <?php foreach(['activo','suspendido','finalizado'] as $st): ?>
              <option value="<?= $st ?>"><?= ucfirst($st) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Diagnóstico</label>
          <input type="text" name="diagnostico" class="form-control" required>
        </div>

        <div class="col-12">
          <label class="form-label">Plan / Indicaciones</label>
          <textarea name="plan" class="form-control" rows="4" placeholder="Indicaciones generales del plan" required></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Inicio</label>
          <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Fin (opcional)</label>
          <input type="date" name="fecha_fin" class="form-control">
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="<?= $paciente_id ? 'historial/ficha.php?id='.$paciente_id.'#tratamientos' : 'tratamientos/listar.php' ?>" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
