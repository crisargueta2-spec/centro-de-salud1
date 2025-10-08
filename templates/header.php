<?php require_once __DIR__.'/../includes/session.php'; ?>
<?php
  $u   = $_SESSION['user'] ?? null;
  $rol = strtolower($u['rol'] ?? ($u['role'] ?? ''));

  $uri    = $_SERVER['REQUEST_URI'] ?? '';
  $active = function(string $needle) use ($uri) {
    return (strpos($uri, $needle) !== false) ? 'active' : '';
  };

  // Permisos por rol
  $canUsuarios       = ($rol === 'admin');
  $canPacientes      = in_array($rol, ['admin','secretaria']);
  $canAntecedentes   = in_array($rol, ['admin','secretaria','medico']);
  $canEspecialistas  = in_array($rol, ['admin','secretaria']);
  $canAsignaciones   = in_array($rol, ['admin','secretaria','medico']);
  $canSeguimientos   = in_array($rol, ['admin','medico']);
  $canTratamientos   = in_array($rol, ['admin','medico']); // secretaria NO ve tratamientos
  $canRecetas        = in_array($rol, ['admin','secretaria','medico']);
  $canHistorial      = in_array($rol, ['admin','secretaria','medico']);

  $dashboard = 'roles/'.($rol ?: 'admin').'_dashboard.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Centro de Salud Sur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="/Centro%20de%20salud%20sur/">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html,body{min-height:100%}
    body{display:flex; margin:0; background:#f5f5f5}
    /* Sidebar */
    .sidebar{width:250px; background:#0d6efd; color:#fff; padding:20px 0; flex:0 0 250px; min-height:100vh; position:sticky; top:0;}
    .sidebar .brand{padding:0 20px 12px; display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.3px}
    .sidebar a{color:#fff; text-decoration:none; display:block; padding:12px 20px}
    .sidebar a:hover,.sidebar a.active{background:#0b5ed7}
    /* Content */
    .content{flex:1; padding:30px}
    .table-card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.1)}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .small-muted{font-size:.9rem;color:#6c757d}
    /* Mobile */
    @media (max-width: 992px){
      .sidebar{position:fixed; left:-260px; transition:.2s}
      .sidebar.open{left:0}
      .content{padding:18px}
      .toggle-btn{position:fixed; left:12px; top:12px; z-index:1000}
    }
    /* Print */
    @media print {
      .sidebar, .toggle-btn, .no-print { display:none !important; }
      .content{ padding:0 !important; }
      .table-card{ box-shadow:none !important; }
    }
  </style>
</head>
<body>

<?php if ($u): ?>
  <aside id="sidebar" class="sidebar">
    <div class="brand">
      <i class="bi bi-hospital me-1"></i> <span>Centro de Salud</span>
    </div>

    <!-- Inicio -->
    <a class="<?= $active('roles/') ?: $active('admin_dashboard.php') ?: $active('medico_dashboard.php') ?: $active('secretaria_dashboard.php') ?>"
       href="<?= htmlspecialchars($dashboard) ?>">
      <i class="bi bi-house-door-fill me-2"></i>Inicio
    </a>

    <!-- 1) Usuarios (solo admin) -->
    <?php if ($canUsuarios) { ?>
      <a class="<?= $active('usuarios/') ?>" href="usuarios/listar.php">
        <i class="bi bi-people-fill me-2"></i>Usuarios
      </a>
    <?php } ?>

    <!-- 2) Pacientes -->
    <?php if ($canPacientes) { ?>
      <a class="<?= $active('pacientes/') ?>" href="pacientes/listar.php">
        <i class="bi bi-person-vcard-fill me-2"></i>Pacientes
      </a>
    <?php } ?>

    <!-- 3) Antecedentes -->
    <?php if ($canAntecedentes) { ?>
      <a class="<?= $active('antecedentes/') ?>" href="antecedentes/listar.php">
        <i class="bi bi-prescription me-2"></i>Antecedentes
      </a>
    <?php } ?>

    <!-- 4) Especialistas -->
    <?php if ($canEspecialistas) { ?>
      <a class="<?= $active('especialista/') ?>" href="especialista/listar.php">
        <i class="bi bi-heart-pulse-fill me-2"></i>Especialistas
      </a>
    <?php } ?>

    <!-- 5) Asignaciones -->
    <?php if ($canAsignaciones) { ?>
      <a class="<?= $active('asignaciones/') ?>" href="asignaciones/listar.php">
        <i class="bi bi-journal-text me-2"></i>Asignaciones
      </a>
    <?php } ?>

    <!-- 6) Seguimientos -->
    <?php if ($canSeguimientos) { ?>
      <a class="<?= $active('seguimientos/') ?>" href="seguimientos/listar.php">
        <i class="bi bi-clipboard2-pulse-fill me-2"></i>Seguimientos
      </a>
    <?php } ?>

    <!-- 7) Tratamientos (médico/admin) -->
    <?php if ($canTratamientos) { ?>
      <a class="<?= $active('tratamientos/') ?>" href="tratamientos/listar.php">
        <i class="bi bi-capsule me-2"></i>Tratamientos
      </a>
    <?php } ?>

    <!-- 8) Recetas -->
    <?php if ($canRecetas) { ?>
      <a class="<?= $active('recetas/') ?>" href="recetas/listar.php">
        <i class="bi bi-file-medical me-2"></i>Recetas
      </a>
    <?php } ?>

    <!-- 9) Historial -->
    <?php if ($canHistorial) { ?>
      <a class="<?= $active('historial/') ?>" href="historial/listar.php">
        <i class="bi bi-archive me-2"></i>Historial médico
      </a>
    <?php } ?>

    <!-- Salir -->
    <a href="salir.php"><i class="bi bi-box-arrow-right me-2"></i>Cambiar usuario</a>
  </aside>
<?php endif; ?>

<main class="content">
  <?php if ($u): ?>
    <button class="btn btn-outline-primary d-lg-none toggle-btn" type="button"
            onclick="document.getElementById('sidebar').classList.toggle('open')">
      <i class="bi bi-list"></i>
    </button>
  <?php endif; ?>
