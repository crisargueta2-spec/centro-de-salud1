<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hospital Nacional de Huehuetenango</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 70px; /* para que no tape el menú fijo */
        background-color: #f9f9f9;
      }
    </style>
</head>
<body>

<!-- Menú de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/index.php">Hospital Huehuetenango</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Alternar navegación"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="/pacientes/registrar.php">Registrar Paciente</a></li>
        <li class="nav-item"><a class="nav-link" href="/pacientes/listar.php">Lista de Pacientes</a></li>
        <li class="nav-item"><a class="nav-link" href="/asignaciones/registrar.php">Asignar Especialista</a></li>
        <li class="nav-item"><a class="nav-link" href="/asignaciones/listar.php">Listado de Asignaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="/seguimientos/registrar.php">Registrar Seguimiento</a></li>
        <li class="nav-item"><a class="nav-link" href="/seguimientos/listar.php">Listado de Seguimientos</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenedor principal para contenido de cada página -->
<div class="container mt-4">
