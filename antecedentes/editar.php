<?php
require_once __DIR__.'/../includes/auth.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conexion->prepare("SELECT * FROM antecedentes WHERE id=?");
$stmt->execute([$id]);
$an = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$an){ http_response_code(404); exit('No encontrado'); }

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre, apellido")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!csrf_validate($_POST['csrf_token'] ?? '')){ http_response_code(400); exit('CSRF'); }

  $paciente_id = (int)($_POST['paciente_id'] ?? 0);
  $tipo = $_POST['tipo'] ?? '';
  $descripcion = trim($_POST['descripcion'] ?? '');
  $fecha = $_POST['fecha_registro'] ?? date('Y-m-d');

  $up = $conexion->prepare("UPDATE antecedentes SET paciente_id=?, tipo=?, descripcion=?, fecha_registro=? WHERE id=?");
  $up->execute([$paciente_id,$tipo,$descripcion,$fecha,$id]);

  header('Location: ../antecedentes/listar.php?ok=2'); // ✅ corregido
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
    <div class="form-card-head">Editar antecedente</div>
    <div class="form-card-body">
      <form method="POST" action="editar.php?id=<?= $an['id'] ?>" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-12">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required>
            <?php foreach($pacientes as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $p['id']==$an['paciente_id']?'selected':'' ?>>
                <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tipo</label>
          <select name="tipo" class="form-select" required>
            <?php foreach(['medicos','quirurgicos','obstetricos','alergicos','familiares','otros'] as $t): ?>
              <option value="<?= $t ?>" <?= $an['tipo']===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha</label>
          <input type="date" name="fecha_registro" class="form-control" value="<?= htmlspecialchars($an['fecha_registro']) ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($an['descripcion']) ?></textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
