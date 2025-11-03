<?php
require_once __DIR__.'/../includes/auth.php';
require_role('admin');
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF'); }
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $role     = $_POST['role'] ?? 'secretaria';

  if ($username && $password) {
    $chk = $conn->prepare("SELECT 1 FROM usuarios WHERE username=?");
    $chk->execute([$username]);
    if ($chk->fetch()) { header('Location: '. (defined('APP_URL')?APP_URL:'') .'usuarios/listar.php?err=dup'); exit; }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (username,password,role) VALUES (?,?,?)");
    $stmt->execute([$username,$hash,$role]);
    header('Location: '. (defined('APP_URL')?APP_URL:'') .'usuarios/listar.php?ok=1'); exit;
  }
}

include __DIR__.'/../templates/header.php';
?>
<style>
  .form-page{display:flex; justify-content:center; }
  .form-card{max-width:700px; width:100%; background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.1); overflow:hidden}
  .form-card-head{background:#0d6efd;color:#fff;padding:12px 16px;font-weight:700}
  .form-card-body{padding:16px}
</style>

<div class="form-page">
  <div class="form-card">
    <div class="form-card-head">Crear usuario</div>
    <div class="form-card-body">
      <form method="POST" action="usuarios/crear.php" class="row g-3">
        <?php csrf_field(); ?>
        <div class="col-12">
          <label class="form-label">Usuario</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Rol</label>
          <select name="role" class="form-select" required>
            <option value="admin">admin</option>
            <option value="medico">medico</option>
            <option value="secretaria">secretaria</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
