<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

// Si el usuario ya está logueado, redirigir según el rol
if (is_logged_in()) {
    redirect_by_role($_SESSION['user']['rol']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Centro de Salud Sur - Iniciar sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-wrapper {
      display: flex;
      background: #fff;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-radius: 15px;
      overflow: hidden;
      max-width: 900px;
      width: 100%;
    }

    .login-left {
      background-color: #007a78;
      color: #fff;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 40px 20px;
    }

    .login-left img {
      width: 160px;
      margin-bottom: 25px;
    }

    .login-left h2 {
      font-weight: 700;
      margin-bottom: 8px;
    }

    .login-left p {
      font-size: 0.95rem;
      opacity: 0.9;
    }

    .login-right {
      flex: 1;
      padding: 40px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-right h3 {
      color: #006b69;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .form-control {
      border-radius: 8px;
    }

    .btn-primary {
      background-color: #007a78;
      border: none;
      border-radius: 8px;
    }

    .btn-primary:hover {
      background-color: #00605e;
    }

    .alert {
      font-size: 0.9rem;
      padding: 6px 10px;
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <!-- PANEL IZQUIERDO -->
    <div class="login-left">
      <img src="img/logo.png" alt="Logo del Centro de Salud Sur">
      <h2><i class="bi bi-hospital"></i> Centro de Salud Sur</h2>
      <p>Sistema de Gestión</p>
    </div>

    <!-- PANEL DERECHO -->
    <div class="login-right">
      <h3><i class="bi bi-person-circle"></i> Iniciar sesión</h3>
      <p class="text-muted">Por favor, inicia sesión para continuar.</p>

      <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="password" required>
        </div>

        <?php if (!empty($_GET['err'])): ?>
          <div class="alert alert-danger mt-2">
            <?php
              if ($_GET['err'] === 'invalid') echo 'Usuario o contraseña incorrectos.';
              elseif ($_GET['err'] === 'csrf') echo 'Error de seguridad, intente nuevamente.';
              elseif ($_GET['err'] === 'db') echo 'Error interno en la base de datos.';
            ?>
          </div>
        <?php endif; ?>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">Recordarme</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right"></i> Entrar
        </button>
      </form>
    </div>
  </div>
</body>
</html>
