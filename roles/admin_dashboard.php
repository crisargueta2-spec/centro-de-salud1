<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$user = user();
include __DIR__ . '/../templates/header.php';
?>

<div class="page-head mb-4">
  <h1>Panel del Administrador</h1>
  <p class="text-muted">Bienvenido, <strong><?= htmlspecialchars($user['username']); ?></strong></p>
</div>

<div class="alert alert-info shadow-sm">
  <i class="bi bi-info-circle-fill"></i>
  Usa el menú lateral izquierdo para acceder a los módulos del sistema.
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
