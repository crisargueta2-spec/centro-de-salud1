<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$user = user();
include __DIR__ . '/../templates/header.php';
?>

<div class="page-head">
  <h1>Panel del Administrador</h1>
  <p class="small-muted">Bienvenido, <strong><?= htmlspecialchars($user['username']); ?></strong></p>
</div>

<div class="container">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-person-vcard-fill fs-1 text-primary"></i>
        <h5 class="mt-3 mb-2">Gestión de Pacientes</h5>
        <a href="../pacientes/listar.php" class="btn btn-primary">Ver</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-journal-text fs-1 text-success"></i>
        <h5 class="mt-3 mb-2">Asignaciones</h5>
        <a href="../asignaciones/listar.php" class="btn btn-success">Ver</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-people-fill fs-1 text-warning"></i>
        <h5 class="mt-3 mb-2">Usuarios</h5>
        <a href="../usuarios/listar.php" class="btn btn-warning">Ver</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
