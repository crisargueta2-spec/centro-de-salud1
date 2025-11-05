<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400); exit('CSRF');
    }

    $paciente_id      = (int)($_POST['paciente_id'] ?? 0);
    $especialista_id  = (int)($_POST['especialista_id'] ?? 0);
    // Si no envían fecha → asumir HOY (para que aparezca en “Hoy”)
    $fecha_asignacion = !empty($_POST['fecha_asignacion']) ? $_POST['fecha_asignacion'] : date('Y-m-d');
    $estado           = trim($_POST['estado'] ?? '');
    $nota             = trim($_POST['nota'] ?? '');

    $stmt = $conexion->prepare("INSERT INTO asignaciones
      (paciente_id, especialista_id, fecha_asignacion, estado, nota)
      VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$paciente_id, $especialista_id, $fecha_asignacion, $estado, $nota]);

    header('Location: listar.php?ok=1'); exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>.form-page{display:flex;justify-content:center}
.form-card{max-width:800px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}
.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
.form-card-body{padding:16px}</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Nueva Asignación</div>
    <div class="form-card-body">
      <form method="POST" action="" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-md-4">
          <label class="form-label">Paciente (ID)</label>
          <input type="number" name="paciente_id" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Especialista (ID)</label>
          <input type="number" name="especialista_id" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha asignación</label>
          <input type="date" name="fecha_asignacion" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <input type="text" name="estado" class="form-control" placeholder="Pendiente / En curso / Cerrada">
        </div>
        <div class="col-12">
          <label class="form-label">Nota</label>
          <textarea name="nota" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="../asignaciones/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
