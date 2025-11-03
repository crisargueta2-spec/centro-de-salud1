<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$user = user();
include __DIR__ . '/../templates/header.php';
?>

<div class="page-head mb-4">
  <h1>Panel del Administrador</h1>
  <p class="small-muted">Bienvenido, <strong><?= htmlspecialchars($user['username']); ?></strong></p>
</div>

<div class="container">
  <div class="row g-4">

    <!-- PACIENTES -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-person-vcard-fill fs-1 text-primary"></i>
        <h5 class="mt-3 mb-2">Gestión de Pacientes</h5>
        <a href="../pacientes/listar.php" class="btn btn-primary">Ver</a>
      </div>
    </div>

    <!-- ASIGNACIONES -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-journal-text fs-1 text-success"></i>
        <h5 class="mt-3 mb-2">Asignaciones</h5>
        <a href="../asignaciones/listar.php" class="btn btn-success">Ver</a>
      </div>
    </div>

    <!-- USUARIOS -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-people-fill fs-1 text-warning"></i>
        <h5 class="mt-3 mb-2">Usuarios</h5>
        <a href="../usuarios/listar.php" class="btn btn-warning">Ver</a>
      </div>
    </div>

    <!-- ANTECEDENTES -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-clipboard-heart fs-1 text-danger"></i>
        <h5 class="mt-3 mb-2">Antecedentes Médicos</h5>
        <a href="../antecedentes/listar.php" class="btn btn-danger">Ver</a>
      </div>
    </div>

    <!-- ESPECIALISTAS -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-person-badge-fill fs-1 text-info"></i>
        <h5 class="mt-3 mb-2">Especialistas</h5>
        <a href="../especialista/listar.php" class="btn btn-info">Ver</a>
      </div>
    </div>

    <!-- RECETAS -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-capsule-pill fs-1 text-secondary"></i>
        <h5 class="mt-3 mb-2">Recetas Médicas</h5>
        <a href="../recetas/listar.php" class="btn btn-secondary">Ver</a>
      </div>
    </div>

    <!-- SEGUIMIENTOS -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-activity fs-1 text-success"></i>
        <h5 class="mt-3 mb-2">Seguimientos</h5>
        <a href="../seguimientos/listar.php" class="btn btn-success">Ver</a>
      </div>
    </div>

    <!-- TRATAMIENTOS -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-heart-pulse-fill fs-1 text-danger"></i>
        <h5 class="mt-3 mb-2">Tratamientos</h5>
        <a href="../tratamientos/listar.php" class="btn btn-danger">Ver</a>
      </div>
    </div>

    <!-- HISTORIAL -->
    <div class="col-md-4">
      <div class="card text-center p-4 shadow-sm">
        <i class="bi bi-archive-fill fs-1 text-dark"></i>
        <h5 class="mt-3 mb-2">Historial Clínico</h5>
        <a href="../historial/listar.php" class="btn btn-dark">Ver</a>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

