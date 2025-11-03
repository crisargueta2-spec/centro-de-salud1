<?php
ob_start(); // Evita errores de encabezado prematuro

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$user = user();

include __DIR__ . '/../templates/header.php';
?>

<div class="container mt-5">
  <div class="text-center mb-4">
    <h1 class="fw-bold text-primary">Panel del Administrador</h1>
    <p class="text-muted">Bienvenido, <strong><?= htmlspecialchars($user['username']); ?></strong></p>
  </div>

  <div class="row g-4 justify-content-center">
    <div class="col-md-3">
      <div class="card text-center shadow-sm p-4">
        <i class="bi bi-person-vcard-fill fs-1 text-primary"></i>
        <h5 class="mt-3 mb-2">Gestión de Pacientes</h5>
        <a href="../pacientes/listar.php" class="btn btn-outline-primary">Ver</a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-center shadow-sm p-4">
        <i class="bi bi-journal-text fs-1 text-success"></i>
        <h5 class="mt-3 mb-2">Asignaciones</h5>
        <a href="../asignaciones/listar.php" class="btn btn-outline-success">Ver</a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-center shadow-sm p-4">
        <i class="bi bi-people-fill fs-1 text-warning"></i>
        <h5 class="mt-3 mb-2">Usuarios</h5>
        <a href="../usuarios/listar.php" class="btn btn-outline-warning">Ver</a>
      </div>
    </div>
  </div>
</div>

<?php
include __DIR__ . '/../templates/footer.php';
ob_end_flush(); // Libera el buffer sin cortar salida
?>

