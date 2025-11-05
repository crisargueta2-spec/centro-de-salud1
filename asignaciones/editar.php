<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM asignaciones WHERE id=?");
$stmt->execute([$id]);
$a = $stmt->fetch();

if (!$a) { http_response_code(404); exit("No encontrado"); }

if ($_SERVER['REQUEST_METHOD']==='POST') {

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400); exit('CSRF');
    }

    $paciente_id     = (int)$_POST['paciente_id'];
    $especialista_id = (int)$_POST['especialista_id'];
    $fecha_cita      = !empty($_POST['fecha_cita']) ? $_POST['fecha_cita'] : date('Y-m-d');
    $prioridad       = $_POST['prioridad'];
    $estado          = $_POST['estado'];

    $up = $conexion->prepare("UPDATE asignaciones
        SET paciente_id=?, especialista_id=?, fecha_cita=?, prioridad=?, estado=?
        WHERE id=?");

    $up->execute([$paciente_id, $especialista_id, $fecha_cita, $prioridad, $estado, $id]);

    header("Location: listar.php?ok=2"); exit;
}

include __DIR__.'/../templates/header.php';
?>

<div class="container py-3">
  <h3>Editar Asignación</h3>

  <form method="POST">
    <?php csrf_field(); ?>

    <label class="form-label">Paciente (ID)</label>
    <input type="number" name="paciente_id" class="form-control"
           value="<?= $a['paciente_id'] ?>" required>

    <label class="form-label mt-2">Especialista (ID)</label>
    <input type="number" name="especialista_id" class="form-control"
           value="<?= $a['especialista_id'] ?>" required>

    <label class="form-label mt-2">Fecha cita</label>
    <input type="date" name="fecha_cita" class="form-control"
           value="<?= htmlspecialchars($a['fecha_cita']) ?>">

    <label class="form-label mt-2">Prioridad</label>
    <select name="prioridad" class="form-select">
      <option value="alta"   <?= $a['prioridad']==='alta'?'selected':'' ?>>Alta</option>
      <option value="media"  <?= $a['prioridad']==='media'?'selected':'' ?>>Media</option>
      <option value="baja"   <?= $a['prioridad']==='baja'?'selected':'' ?>>Baja</option>
    </select>

    <label class="form-label mt-2">Estado</label>
    <select name="estado" class="form-select">
      <option value="pendiente" <?= $a['estado']==='pendiente'?'selected':'' ?>>Pendiente</option>
      <option value="atendido"  <?= $a['estado']==='atendido'?'selected':'' ?>>Atendido</option>
      <option value="cancelado" <?= $a['estado']==='cancelado'?'selected':'' ?>>Cancelado</option>
    </select>

    <div class="mt-3">
      <a href="listar.php" class="btn btn-secondary">Cancelar</a>
      <button class="btn btn-primary">Actualizar</button>
    </div>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
