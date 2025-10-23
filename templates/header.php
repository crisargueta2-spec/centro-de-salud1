<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

$u   = $_SESSION['user'] ?? null;
$rol = strtolower($u['rol'] ?? ($u['role'] ?? ''));

$uri = $_SERVER['REQUEST_URI'] ?? '';
$active = function (string $needle) use ($uri) {
  return (strpos($uri, $needle) !== false) ? 'active' : '';
};

// Permisos por rol
$canUsuarios       = ($rol === 'admin');
$canPacientes      = in_array($rol, ['admin','secretaria']);
$canAntecedentes   = in_array($rol, ['admin','secretaria','medico']);
$canEspecialistas  = in_array($rol, ['admin','secretaria']);
$canAsignaciones   = in_array($rol, ['admin','secretaria','medico']);
$canSeguimientos   = in_array($rol, ['admin','medico']);
$canTratamientos   = in_array($rol, ['admin','medico']);
$canRecetas        = in_array($rol, ['admin','secretaria','medico']);
$canHistorial      = in_array($rol, ['admin','secretaria','medico']);

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

  <!-- Estilos generales -->
  <style>
    html, body { min-height:100%; }
    body { display:flex; margin:0; background:#f5f5f5; }

    /* SIDEBAR */
    .sidebar {
      width:250px; background:#007a78; color:#fff;
      padding:20px 0; flex:0 0 250px; min-height:100vh;
      position:sticky; top:0;
      box-shadow:0 2px 10px rgba(0,0,0,.1);
    }
    .sidebar .brand {
      padding:0 20px 12px;
      display:flex; align-items:center; gap:10px;
      font-weight:800; font-size:1.1rem;
    }
    .sidebar a {
      color:#fff; text-dec
