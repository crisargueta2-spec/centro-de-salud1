<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

// Si hay sesión activa, redirige según el rol
if (is_logged_in()) {
    redirect_by_role($_SESSION['user']['rol']);
}

include __DIR__ . '/templates/header_public.php'; // Nuevo encabezado público
?>

<div class="container text-center py-5">
  <h1 class="text-primary fw-bold mb-3">Centro de Salud Sur de Huehuetenango</h1>
  <p class="lead mb-4">Sistema web para referir pacientes, asignar especialistas y dar seguimiento a consultas médicas internas.</p>

  <div class="row mt-5">
    <div class="col-md-6">
      <h3 class="text-success">Misión</h3>
      <p>Brindar atención médica eficiente, oportuna y humana a los pacientes de la región, mediante el uso de herramientas tecnológicas que mejoren la gestión interna del centro de salud.</p>
    </div>
    <div class="col-md-6">
      <h3 class="text-info">Visión</h3>
      <p>Ser un referente regional en innovación tecnológica aplicada a la salud pública, optimizando los procesos médicos y administrativos para un mejor servicio a la comunidad.</p>
    </div>
  </div>

  <div class="mt-5">
    <a href="login.php" class="btn btn-primary btn-lg">
      <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
    </a>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
