<?php
require_once __DIR__.'/includes/auth.php';
require_login();
$u   = user();
$rol = strtolower($u['rol'] ?? ($u['role'] ?? ''));
$dashboard = 'roles/'. ($rol ?: 'admin') .'_dashboard.php';

include __DIR__.'/templates/header.php';
?>
<div class="container" style="max-width:760px; margin-top:20px;">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">Cambiar de usuario</div>
    <div class="card-body">
      <p class="mb-3">¿Deseas cerrar tu sesión actual y volver a la pantalla de inicio de sesión?</p>
      <div class="d-flex gap-2 justify-content-end">
        <a class="btn btn-secondary" href="<?= htmlspecialchars($dashboard) ?>">Cancelar</a>
        <!-- Esta opción te desconecta y te lleva al login -->
        <a class="btn btn-primary" href="logout.php?to=index">Aceptar</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__.'/templates/footer.php'; ?>
