<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/csrf.php';
if (is_logged()) { redirect_by_role(user()['rol']); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión — Centro de Salud Sur</title>
  <base href="/Centro%20de%20salud%20sur/">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{ --teal:#007a78; --teal-700:#046e6c; --shadow:0 4px 14px rgba(0,0,0,.14); --radius:18px; }
    body{ background:#f8f9fa; }
    .login-page{ min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
    .login-card{ display:flex; overflow:hidden; border:0; border-radius:var(--radius); box-shadow:var(--shadow); max-width: 980px; width:100%; background:#fff; }
    .login-left{ background:var(--teal); color:#fff; padding:48px 32px; width:42%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; }
    .login-left .logo{ width:180px; height:auto; object-fit:contain; margin-bottom:18px; }
    .login-left .fallback-icon{ font-size:86px; opacity:.95; margin-bottom:14px; }
    .login-right{ padding:40px 36px; width:58%; background:#fff }
    .login-title{ color:var(--teal); font-weight:800; }
    .form-control{ border-radius:0; border:0; border-bottom:1px solid #ced4da; padding-left:0; padding-right:0; }
    .form-control:focus{ box-shadow:none; border-color:var(--teal); }
    .btn-outline-teal{
      --bs-btn-color: var(--teal); --bs-btn-border-color: var(--teal);
      --bs-btn-hover-bg: var(--teal); --bs-btn-hover-border-color: var(--teal); --bs-btn-hover-color:#fff;
      --bs-btn-active-bg: var(--teal-700); --bs-btn-active-border-color: var(--teal-700);
    }
    .alert{ border-radius:10px }
    @media (max-width: 992px){
      .login-card{ flex-direction:column; }
      .login-left, .login-right{ width:100% }
      .login-left{ padding:34px 28px }
    }
  </style>
</head>
<body>
<main class="login-page">
  <div class="login-card">
    <div class="login-left">
      <img src="img/logo.png" alt="Logo Centro de Salud" class="logo" onerror="this.style.display='none'">
      <i class="bi bi-hospital-fill fallback-icon"></i>
      <h4>Centro de Salud Sur</h4>
      <p class="mb-0" style="opacity:.95">Sistema de Gestión</p>
    </div>
    <div class="login-right">
      <h3 class="text-center mb-4 login-title">Iniciar sesión</h3>

      <?php if (!empty($_GET['err'])): ?>
        <div class="alert alert-danger">Usuario o contraseña inválidos.</div>
      <?php elseif (!empty($_GET['msg']) && $_GET['msg']==='login'): ?>
        <div class="alert alert-success">Por favor, inicia sesión para continuar.</div>
      <?php elseif (!empty($_GET['msg']) && $_GET['msg']==='logout'): ?>
        <div class="alert alert-info">Sesión cerrada correctamente. Puedes iniciar nuevamente.</div>
      <?php endif; ?>

      <form action="login.php" method="POST" novalidate>
        <?php csrf_field(); ?>
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Usuario" name="username" required autocomplete="username">
        </div>
        <div class="mb-2">
          <input type="password" class="form-control" placeholder="Contraseña" name="password" required autocomplete="current-password">
        </div>
        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" value="1" id="remember">
          <label class="form-check-label" for="remember"><strong>Recordarme</strong></label>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-outline-teal">Entrar</button>
        </div>
      </form>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
