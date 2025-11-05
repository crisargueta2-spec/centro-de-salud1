<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM asignaciones WHERE id=?");
$stmt->execute([$id]);
$a = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$a) {
    http_response_code(404);
    exit("No encontrado");
}

$pacientes = $conexion->query("SELECT id,nombre,apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$especialistas = $conexion->query("SELECT id,nombre,especialidad FROM especialistas ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD']==='POST') {

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit("CSRF");
    }

    $paciente_id     = (int)$_POST['paciente_id'];
    $especialista_id = (int)$_POST['especialista_id'];
    $fecha_cita      = $_POST['fecha_cita'];
    $prioridad       = $_POST['prioridad'] ?: null;
    $estado          = $_POST['estado'];

    $up = $conexion->prepare("
        UPDATE asignaciones
        SET paciente_id=?, especialista_id=?, fecha_cita=?, prioridad=?, estado=?
        WHERE id=?
    ");
    $up->execute([$paciente_id,$especialista_id,$fecha_cita,$prioridad,$estado,$id]);

    header("Location: listar.php?ok=2");
    exit;
}

include __DIR__.'/../templates/header.php';
?>

<style>
.form-page{display:flex;justify-content:center}
.form-card{max-width:700px;background:white;border-radius:10px;
           box-shadow:0 2px 8px rgba(0,0,0,.1)}
.form-card-head{background:#0d6efd;color:white;padding:12px;font-weight:700}
.form-card-body{padding:16px}
</style>

<div class="form-page">
  <div class="form-card">

    <div class="form-card-head">Editar Asignación</div>

    <div class="form-card-body">

      <form method="POST" class="row g-3">

        <?php csrf_field(); ?>

        <div class="col-12">
          <label class="form-label">Paciente</label>
          <select name="paciente_id" class="form-select" required>
            <?php foreach($pacientes as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $p['id']==$a['paciente_id']?'selected':'' ?>>
              <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Especialista</label>
          <select name="especialista_id" class="form-select" required>
            <?php foreach($especialistas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= $e['id']==$a['especialista_id']?'selected':'' ?>>
              <?= htmlspecialchars($e['nombre'].' — '.$e['especialidad']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha cita</label>
          <input type="date"
            name="fecha_cita"
            value="<?= htmlspecialchars($a['fecha_cita']) ?>"
            class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Prioridad</label>
          <select name="prioridad" class="form-select">
            <option value="">—</option>
            <option value="alta"  <?= $a['prioridad']==='alta'?'selected':'' ?>>Alta</option>
            <option value="media" <?= $a['prioridad']==='media'?'selected':'' ?>>Media</option>
            <option value="baja"  <?= $a['prioridad']==='baja'?'selected':'' ?>>Baja</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="pendiente" <?= $a['estado']==='pendiente'?'selected':'' ?>>Pendiente</option>
            <option value="atendido"  <?= $a['estado']==='atendido'?'selected':'' ?>>Atendido</option>
            <option value="cancelado" <?= $a['estado']==='cancelado'?'selected':'' ?>>Cancelado</option>
          </select>
        </div>

        <div class="col-12 text-end">
          <a href="/asignaciones/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary">Actualizar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
