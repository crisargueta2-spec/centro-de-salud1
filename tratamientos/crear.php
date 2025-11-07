<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$base = basename(__DIR__);

$pacientes = $conexion->query("SELECT id,nombre,apellido FROM pacientes ORDER BY nombre,apellido")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!csrf_validate($_POST['csrf'] ?? '')){ http_response_code(400); exit('CSRF'); }
    $paciente_id=(int)($_POST['paciente_id']??0);
    $diagnostico=trim($_POST['diagnostico']??'');
    $plan=trim($_POST['plan']??'');
    $estado=$_POST['estado']??'activo';
    $fecha_inicio=$_POST['fecha_inicio']?:date('Y-m-d');
    $fecha_fin=$_POST['fecha_fin']?:null;

    $stmt=$conexion->prepare("INSERT INTO tratamientos(paciente_id,diagnostico,plan,estado,fecha_inicio,fecha_fin)
                              VALUES(?,?,?,?,?,?)");
    $stmt->execute([$paciente_id,$diagnostico,$plan,$estado,$fecha_inicio,$fecha_fin]);
    header("Location: $base/listar.php?ok=1");
    exit;
}

include __DIR__.'/../templates/header.php';
?>
<div class="container mt-4">
  <h2>Nuevo Tratamiento</h2>
  <form method="POST" action="<?= $base ?>/crear.php" class="row g-3">
    <?php csrf_field(); ?>
    <div class="col-md-6">
      <label class="form-label">Paciente</label>
      <select name="paciente_id" class="form-select" required>
        <option value="">Seleccione...</option>
        <?php foreach($pacientes as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-select">
        <option value="activo">Activo</option>
        <option value="suspendido">Suspendido</option>
        <option value="finalizado">Finalizado</option>
      </select>
    </div>
    <div class="col-12">
      <label class="form-label">Diagnóstico</label>
      <input type="text" name="diagnostico" class="form-control" required>
    </div>
    <div class="col-12">
      <label class="form-label">Plan</label>
      <textarea name="plan" class="form-control" rows="4" required></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Inicio</label>
      <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Fin</label>
      <input type="date" name="fecha_fin" class="form-control">
    </div>
    <div class="col-12 text-end">
      <a href="<?= $base ?>/listar.php" class="btn btn-secondary">Cancelar</a>
      <button class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
