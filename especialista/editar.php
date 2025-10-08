<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM especialistas WHERE id=?");
$stmt->execute([$id]);
$e = $stmt->fetch();
if(!$e){ http_response_code(404); exit('No encontrado'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  if (!csrf_validate($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF'); }
  $nombre = trim($_POST['nombre'] ?? '');
  $especialidad = trim($_POST['especialidad'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $email = trim($_POST['email'] ?? '');

  $up = $conn->prepare("UPDATE especialistas SET nombre=?, especialidad=?, telefono=?, email=? WHERE id=?");
  $up->execute([$nombre,$especialidad,$telefono,$email,$id]);
  header('Location: '. (defined('APP_URL')?APP_URL:'') .'especialista/listar.php?ok=2'); exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>.form-page{display:flex; justify-content:center}.form-card{max-width:700px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}.form-card-body{padding:16px}</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Editar Especialista</div>
    <div class="form-card-body">
      <form method="POST" action="especialista/editar.php?id=<?= $e['id'] ?>" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($e['nombre']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Especialidad</label>
          <input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($e['especialidad']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($e['telefono']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($e['email']) ?>">
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="especialista/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
