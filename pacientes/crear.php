<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit('CSRF');
    }

    $nombre            = trim($_POST['nombre'] ?? '');
    $apellido          = trim($_POST['apellido'] ?? '');
    $fecha_nacimiento  = $_POST['fecha_nacimiento'] ?? null;
    $genero            = $_POST['genero'] ?? null;
    $medico_referente  = trim($_POST['medico_referente'] ?? '');
    $motivo            = trim($_POST['motivo'] ?? '');
    $fecha_referencia  = $_POST['fecha_referencia'] ?? null;

    $stmt = $conexion->prepare("INSERT INTO pacientes 
        (nombre, apellido, fecha_nacimiento, genero, medico_referente, motivo, fecha_referencia)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $genero, $medico_referente, $motivo, $fecha_referencia]);

    header('Location: listar.php?ok=1');
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
    <div class="form-card-head">Registrar Paciente</div>
    <div class="form-card-body">
      <form method="POST" action="" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellido</label>
          <input type="text" name="apellido" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Género</label>
          <select name="genero" class="form-select">
  <option value="">—</option>
  <option value="M">Masculino</option>
  <option value="F">Femenino</option>
</select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha referencia</label>
          <input type="date" name="fecha_referencia" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Médico referente</label>
          <input type="text" name="medico_referente" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Motivo</label>
          <textarea name="motivo" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
