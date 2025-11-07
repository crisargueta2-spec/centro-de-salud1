<?php
require_once __DIR__.'/../includes/auth.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }  // ✅ CSRF FIX
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!csrf_validate($_POST['csrf_token'] ?? '')){ http_response_code(400); exit('CSRF'); }

  $paciente_id = (int)($_POST['paciente_id'] ?? 0);
  $tipo = $_POST['tipo'] ?? '';
  $descripcion = trim($_POST['descripcion'] ?? '');
  $fecha = $_POST['fecha_registro'] ?? date('Y-m-d');

  $stmt = $conexion->prepare("INSERT INTO antecedentes (paciente_id,tipo,descripcion,fecha_registro) VALUES (?,?,?,?)");
  $stmt->execute([$paciente_id,$tipo,$descripcion,$fecha]);

  header('Location: antecedentes/listar.php?ok=1');
  exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>
.form-page{display:flex;justify-content:center}
.form-card{max-width:720px;width:100%;background:#fff;border-radius:10px;
box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}
.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
.form-card-body{padding:16px}
</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Nuevo antecedente</div>
    <div class="form-card-body">
      <form method="POST" action="antecedentes/crear.php" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-12">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required>
            <option value="">— Seleccione —</option>
            <?php foreach($pacientes as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tipo</label>
          <select name="tipo" class="form-select" required>
            <?php foreach(['medicos','quirurgicos','obstetricos','alergicos','familiares','otros'] as $t): ?>
              <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha</label>
          <input type="date" name="fecha_registro" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="antecedentes/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
