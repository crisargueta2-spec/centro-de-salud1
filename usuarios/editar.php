<?php
require_once __DIR__.'/../includes/auth.php';
require_role('admin');
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id,username,role FROM usuarios WHERE id=?");
$stmt->execute([$id]);
$u = $stmt->fetch();
if(!$u){ http_response_code(404); exit('No encontrado'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF'); }
  $username = trim($_POST['username'] ?? '');
  $role     = $_POST['role'] ?? 'secretaria';
  $password = $_POST['password'] ?? '';

  $chk = $conn->prepare("SELECT 1 FROM usuarios WHERE username=? AND id<>?");
  $chk->execute([$username,$id]);
  if ($chk->fetch()) { header('Location: '. (defined('APP_URL')?APP_URL:'') .'usuarios/listar.php?err=dup'); exit; }

  if ($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $up = $conn->prepare("UPDATE usuarios SET username=?, role=?, password=? WHERE id=?");
    $up->execute([$username,$role,$hash,$id]);
  } else {
    $up = $conn->prepare("UPDATE usuarios SET username=?, role=? WHERE id=?");
    $up->execute([$username,$role,$id]);
  }
  header('Location: '. (defined('APP_URL')?APP_URL:'') .'usuarios/listar.php?ok=2'); exit;
}

include __DIR__.'/../templates/header.php';
?>
<style>.form-page{display:flex; justify-content:center}.form-card{max-width:700px;width:100%;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);overflow:hidden}.form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}.form-card-body{padding:16px}</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Editar usuario</div>
    <div class="form-card-body">
      <form method="POST" action="usuarios/editar.php?id=<?= $u['id'] ?>" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-12">
          <label class="form-label">Usuario</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nueva contraseña (opcional)</label>
          <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
        </div>
        <div class="col-md-6">
          <label class="form-label">Rol</label>
          <select name="role" class="form-select" required>
            <?php foreach(['admin','medico','secretaria'] as $r): ?>
              <option value="<?= $r ?>" <?= $u['role']===$r?'selected':'' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
