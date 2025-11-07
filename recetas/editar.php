<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID inválido'); }

$st = $conexion->prepare("SELECT * FROM recetas WHERE id=?");
$st->execute([$id]);
$receta = $st->fetch(PDO::FETCH_ASSOC);
if (!$receta) { http_response_code(404); exit('Receta no encontrada'); }

$items = $conexion->prepare("SELECT * FROM receta_items WHERE receta_id=? ORDER BY id");
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

$pacientes = $conexion->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(!csrf_validate($_POST['csrf_token'] ?? '')){ http_response_code(400); exit('CSRF inválido'); }

  $paciente_id = (int)$_POST['paciente_id'];
  $fecha_emision = $_POST['fecha_emision'];
  $obs = trim($_POST['observaciones'] ?? '');

  $upd = $conexion->prepare("UPDATE recetas SET paciente_id=?, fecha_emision=?, observaciones=? WHERE id=?");
  $upd->execute([$paciente_id, $fecha_emision, $obs, $id]);

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
<style>.form-page{display:flex;justify-content:center}.form-card{max-width:1000px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}.form-card-body{padding:16px}</style>
<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Editar receta</div>
    <div class="form-card-body">
      <form method="POST" class="row g-3">
        <?php csrf_field(); ?>

        <div class="col-md-6">
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

        <div class="col-md-3">
          <label class="form-label">Fecha de emisión</label>
          <input type="date" name="fecha_emision" class="form-control" value="<?= htmlspecialchars($receta['fecha_emision']) ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="2"><?= htmlspecialchars($receta['observaciones']) ?></textarea>
        </div>

        <div class="col-12">
          <hr><h5>Medicamentos</h5>
          <?php for($i=0;$i<6;$i++): 
            $it = $items[$i] ?? ['medicamento'=>'','presentacion'=>'','dosis'=>'','frecuencia'=>'','via'=>'','duracion'=>'','indicaciones'=>'']; ?>
            <div class="row g-2 align-items-end mb-1">
              <div class="col-lg-3"><input class="form-control" name="medicamento[]" placeholder="Medicamento" value="<?= htmlspecialchars($it['medicamento']) ?>"></div>
              <div class="col-lg-2"><input class="form-control" name="presentacion[]" placeholder="Presentación" value="<?= htmlspecialchars($it['presentacion']) ?>"></div>
              <div class="col-lg-2"><input class="form-control" name="dosis[]" placeholder="Dosis" value="<?= htmlspecialchars($it['dosis']) ?>"></div>
              <div class="col-lg-2"><input class="form-control" name="frecuencia[]" placeholder="Frecuencia" value="<?= htmlspecialchars($it['frecuencia']) ?>"></div>
              <div class="col-lg-1"><input class="form-control" name="via[]" placeholder="Vía" value="<?= htmlspecialchars($it['via']) ?>"></div>
              <div class="col-lg-2"><input class="form-control" name="duracion[]" placeholder="Duración" value="<?= htmlspecialchars($it['duracion']) ?>"></div>
              <div class="col-12"><input class="form-control" name="indicaciones[]" placeholder="Indicaciones" value="<?= htmlspecialchars($it['indicaciones']) ?>"></div>
            </div>
            <?php if($i<5): ?><hr class="text-muted"><?php endif; ?>
          <?php endfor; ?>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="/recetas/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
