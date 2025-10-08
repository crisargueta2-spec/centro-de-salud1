<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

// listas select
$pacientes = $conn->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre,apellido")->fetchAll(PDO::FETCH_ASSOC);
$especialistas = $conn->query("SELECT id, nombre, especialidad FROM especialistas ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
  if (!csrf_validate($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF'); }
  $paciente_id = (int)($_POST['paciente_id'] ?? 0);
  $especialista_id = (int)($_POST['especialista_id'] ?? 0);
  $fecha_cita = $_POST['fecha_cita'] ?? null;
  $prioridad = $_POST['prioridad'] ?? null;

  $stmt = $conn->prepare("INSERT INTO asignaciones (paciente_id,especialista_id,fecha_cita,prioridad) VALUES (?,?,?,?)");
  $stmt->execute([$paciente_id,$especialista_id,$fecha_cita,$prioridad]);
  header('Location: '. (defined('APP_URL')?APP_URL:'') .'asignaciones/listar.php?ok=1'); exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>.form-page{display:flex; justify-content:center}.form-card{max-width:700px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}.form-card-body{padding:16px}</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Nueva Asignación</div>
    <div class="form-card-body">
      <form method="POST" action="asignaciones/crear.php" class="row g-3">
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
        <div class="col-12">
          <label class="form-label">Especialista</label>
          <select name="especialista_id" class="form-select" required>
            <option value="">— Seleccione —</option>
            <?php foreach($especialistas as $e): ?>
              <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre'].' — '.$e['especialidad']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Fecha de cita</label>
          <input type="date" name="fecha_cita" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Prioridad</label>
          <select name="prioridad" class="form-select">
            <option value="">—</option>
            <option>alta</option>
            <option>media</option>
            <option>baja</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="asignaciones/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
