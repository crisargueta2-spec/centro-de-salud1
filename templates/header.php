<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

$u   = $_SESSION['user'] ?? null;
$rol = strtolower($u['rol'] ?? ($u['role'] ?? ''));

$uri = $_SERVER['REQUEST_URI'] ?? '';
$active = function (string $needle) use ($uri) {
  return (strpos($uri, $needle) !== false) ? 'active' : '';
};

// Dashboard base
$dashboard = APP_URL . 'roles/' . ($rol ?: 'admin') . '_dashboard.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Centro de Salud Sur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="<?= APP_URL ?>">

  <!-- Librerías -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Estilos originales -->
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      display: flex;
      min-height: 100vh;
      background-color: #f8f9fa;
    }

    .sidebar {
  width: 250px;
  background: linear-gradient(180deg, #0090db 0%, #00bfff 100%);
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 0;
  box-shadow: 2px 0 6px rgba(0,0,0,0.1);
}

    .sidebar .brand {
      font-size: 1.3rem;
      font-weight: bold;
      text-align: center;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .sidebar a {
      color: #fff;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 20px;
      text-decoration: none;
      transition: background 0.2s;
      font-weight: 500;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255,255,255,0.1);
    }

    .sidebar i {
      font-size: 1.2rem;
    }

    main {
      flex: 1;
      padding: 30px;
      background-color: #f9f9f9;
    }

    .page-head h1 {
      font-weight: 700;
      color: #007a78;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="brand">
      <i class="bi bi-hospital"></i> Centro de Salud Sur
    </div>

    <a href="<?= $dashboard ?>" class="<?= $active('dashboard') ?>"><i class="bi bi-speedometer2"></i> Inicio</a>
    <a href="<?= APP_URL ?>pacientes/listar.php" class="<?= $active('pacientes') ?>"><i class="bi bi-person-vcard-fill"></i> Pacientes</a>
    <a href="<?= APP_URL ?>asignaciones/listar.php" class="<?= $active('asignaciones') ?>"><i class="bi bi-journal-text"></i> Asignaciones</a>
    <a href="<?= APP_URL ?>usuarios/listar.php" class="<?= $active('usuarios') ?>"><i class="bi bi-people-fill"></i> Usuarios</a>
    <a href="<?= APP_URL ?>antecedentes/listar.php" class="<?= $active('antecedentes') ?>"><i class="bi bi-clipboard-heart"></i> Antecedentes</a>
    <a href="<?= APP_URL ?>especialista/listar.php" class="<?= $active('especialista') ?>"><i class="bi bi-person-badge-fill"></i> Especialistas</a>
    <a href="<?= APP_URL ?>recetas/listar.php" class="<?= $active('recetas') ?>"><i class="bi bi-capsule-pill"></i> Recetas</a>
    <a href="<?= APP_URL ?>seguimientos/listar.php" class="<?= $active('seguimientos') ?>"><i class="bi bi-activity"></i> Seguimientos</a>
    <a href="<?= APP_URL ?>tratamientos/listar.php" class="<?= $active('tratamientos') ?>"><i class="bi bi-heart-pulse-fill"></i> Tratamientos</a>
    <a href="<?= APP_URL ?>historial/listar.php" class="<?= $active('historial') ?>"><i class="bi bi-archive-fill"></i> Historial</a>

    <hr class="text-light mx-3">
    <a href="<?= APP_URL ?>logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
  </div>

  <main>
