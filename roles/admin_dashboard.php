<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../templates/header.php';
$user = user();
?>

<div class="container py-4">
  <div class="topbar mb-4">
    <h2 class="fw-bold text-success">Panel del Administrador</h2>
    <div class="small-muted">
      👤 <?= htmlspecialchars($user['username']); ?> (<?= htmlspecialchars($user['rol']); ?>)
    </div>
  </div>

  <div class="row g-4">
    <!-- Gestión de Pacientes -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-person-lines-fill fs-1 text-success"></i>
        <h5 class="mt-3">Gestión de Pacientes</h5>
        <a href="<?= APP_URL ?>pacientes/listar.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>

    <!-- Asignaciones -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-clipboard2-pulse fs-1 text-success"></i>
        <h5 class="mt-3">Asignaciones</h5>
        <a href="<?= APP_URL ?>asignaciones/listar.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>

    <!-- Usuarios -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-people-fill fs-1 text-success"></i>
        <h5 class="mt-3">Usuarios</h5>
        <a href="<?= APP_URL ?>usuarios/listar.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
