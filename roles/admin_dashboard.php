<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$user = user();
include __DIR__ . '/../templates/header.php';
?>

<div class="page-head mb-4">
  <h1 class="fw-bold text-primary">Panel del Administrador</h1>
  <p class="text-muted">Bienvenido, <strong><?= htmlspecialchars($user['username']); ?></strong></p>
</div>

<!-- Bloque principal -->
<div class="container">
  <div class="card shadow-sm p-4 mb-4">
    <h3 class="text-center text-primary mb-3"><i class="bi bi-hospital"></i> Centro de Salud Sur de Huehuetenango</h3>
    <p class="text-center text-secondary mb-4">
      Sistema web para referir pacientes, asignar especialistas y dar seguimiento a consultas médicas internas.
    </p>

    <div class="row text-center mt-4">
      <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100 bg-light">
          <h4 class="text-info"><i class="bi bi-heart-pulse"></i> Misión</h4>
          <p class="mb-0">
            Brindar atención médica eficiente, oportuna y humana a los pacientes de la región, 
            mediante el uso de herramientas tecnológicas que mejoren la gestión interna del centro de salud.
          </p>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100 bg-light">
          <h4 class="text-primary"><i class="bi bi-eye-fill"></i> Visión</h4>
          <p class="mb-0">
            Ser un referente regional en innovación tecnológica aplicada a la salud pública, 
            optimizando los procesos médicos y administrativos para un mejor servicio a la comunidad.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info text-center shadow-sm">
    <i class="bi bi-info-circle-fill"></i>
    Usa el menú lateral izquierdo para acceder a los módulos del sistema.
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
