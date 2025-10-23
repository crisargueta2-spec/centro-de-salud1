<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
$user = user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Administrador — Centro de Salud Sur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f4f6f8; font-family: 'Segoe UI', sans-serif; }
    .navbar { background:#007a78; }
    .navbar-brand, .nav-link, .navbar-text { color:#fff !important; }
    .dashboard-card { border-radius:16px; box-shadow:0 4px 14px rgba(0,0,0,.1); transition:.3s; }
    .dashboard-card:hover { transform:translateY(-3px); }
    .icon { font-size:42px; color:#007a78; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Centro de Salud Sur</a>
    <div class="d-flex">
      <span class="navbar-text me-3">👤 <?php echo htmlspecialchars($user['username']); ?> (<?php echo $user['rol']; ?>)</span>
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4 text-center text-success">Panel del Administrador</h2>
  <div class="row g-4 justify-content-center">
    <div class="col-md-3">
      <div class="card dashboard-card text-center p-4">
        <i class="bi bi-person-lines-fill icon"></i>
        <h5 class="mt-3">Gestión de Pacientes</h5>
        <a href="../pacientes.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-card text-center p-4">
        <i class="bi bi-clipboard2-pulse icon"></i>
        <h5 class="mt-3">Asignaciones</h5>
        <a href="../asignaciones.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-card text-center p-4">
        <i class="bi bi-people-fill icon"></i>
        <h5 class="mt-3">Usuarios</h5>
        <a href="../usuarios.php" class="btn btn-outline-success mt-3">Ver</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
