<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID inválido'); }

// Cargar receta e ítems
$st = $conexion->prepare("SELECT * FROM recetas WHERE id=?");
$st->execute([$id]);
$receta = $st->fetch(PDO::FETCH_ASSOC);
if (!$receta) { http_response_code(404); exit('No encontrada'); }

$items = $conexion->prepare("SELECT * FROM receta_items WHERE receta_id=? ORDER BY id");
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(!csrf_validate($_POST['csrf_token'] ?? '')){ http_response_code(400); exit('CSRF inválido'); }

  $paciente_id   = (int)$_POST['paciente_id'];
  $fecha_emision = $_POST['fecha_emision'];
  $obs           = trim($_POST['observaciones'] ?? '');

  // Actualizar receta
  $upd = $conexion->prepare("UPDATE recetas SET paciente_id=?, fecha_emision=?, observaciones=? WHERE id=?");
  $upd->execute([$paciente_id, $fecha_emision, $obs, $id]);

  // Borrar e insertar de nuevo los ítems
  $conexion->prepare("DELETE FROM receta_items WHERE receta_id=?")->execute([$id]);

  $it = $conexion->prepare("INSERT INTO receta_items (receta_id, medicamento, presentacion, dosis, frecuencia, via, duracion, indicaciones)
                            VALUES (?,?,?,?,?,?,?,?)");
  $n = count($_POST['medicamento'] ?? []);
  for ($i=0; $i<$n; $i++) {
    $m = trim($_POST['medicamento'][$i]);
    if ($m === '') continue;
    $it->execute([
      $id,
      $m,
      $_POST['presentacion'][$i] ?? '',
      $_POST['dosis'][$i] ?? '',
      $_POST['frecuencia'][$i] ?? '',
      $_POST['via'][$i] ?? '',
      $_POST['duracion'][$i] ?? '',
      $_POST['indicaciones'][$i] ?? ''
    ]);
  }

  header('Location: /recetas/listar.php?ok=editado');
  exit;
}
include __DIR__.'/../templates/header.php';
?>
<div class="container mt-4">
  <h3>Editar receta</h3>
  <form method="POST">
    <?php csrf_field(); ?>
    <div class="mb-3">
      <label class="form-label">Paciente</label>
      <select name="paciente_id" class="form-select" required>
        <option value="">— Seleccione —</option>
        <?php foreach($pacientes as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $p['id']==$receta['paciente_id']?'selected':'' ?>>
            <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha emisión</label>
      <input type="date" name="fecha_emision" class="form-control" value="<?= htmlspecialchars($receta['fecha_emision']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="2"><?= htmlspecialchars($receta['observaciones']) ?></textarea>
    </div>

    <hr>
    <h5>Medicamentos</h5>
    <?php for($i=0; $i<6; $i++):
      $it = $items[$i] ?? ['medicamento'=>'','presentacion'=>'','dosis'=>'','frecuencia'=>'','via'=>'','duracion'=>'','indicaciones'=>'']; ?>
      <div class="row g-2 mb-2">
        <div class="col-md-3"><input class="form-control" name="medicamento[]" placeholder="Medicamento" value="<?= htmlspecialchars($it['medicamento']) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="presentacion[]" placeholder="Presentación" value="<?= htmlspecialchars($it['presentacion']) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="dosis[]" placeholder="Dosis" value="<?= htmlspecialchars($it['dosis']) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="frecuencia[]" placeholder="Frecuencia" value="<?= htmlspecialchars($it['frecuencia']) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="via[]" placeholder="Vía" value="<?= htmlspecialchars($it['via']) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="duracion[]" placeholder="Duración" value="<?= htmlspecialchars($it['duracion']) ?>"></div>
        <div class="col-12"><input class="form-control" name="indicaciones[]" placeholder="Indicaciones" value="<?= htmlspecialchars($it['indicaciones']) ?>"></div>
      </div>
    <?php endfor; ?>

    <div class="d-flex justify-content-end gap-2 mt-3">
      <a href="/recetas/listar.php" class="btn btn-secondary">Cancelar</a>
      <button class="btn btn-primary">Guardar cambios</button>
    </div>
  </form>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
