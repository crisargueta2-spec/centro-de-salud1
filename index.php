<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

// Si hay sesión activa, redirigir según el rol
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
  <title>Centro de Salud Sur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(180deg, #00b4db 0%, #0083b0 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-container {
      background-color: #ffffff;
      border-radius: 20px;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
      padding: 40px 50px;
      text-align: center;
      max-width: 450px;
      width: 100%;
    }

    .login-container img {
      width: 80px;
      margin-bottom: 15px;
    }

    .login-container h2 {
      color: #0078b0;
      font-weight: 700;
    }

    .login-container p {
      color: #6c757d;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-login {
      background-color: #00a2d3;
      border: none;
      border-radius: 10px;
      padding: 10px;
      font-weight: 600;
      transition: background 0.3s;
    }

    .btn-login:hover {
      background-color: #007fa3;
    }

    .small-muted {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 15px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <!-- LOGO o DIBUJO -->
    <img src="img/logo.png" alt="Logo del Centro de Salud Sur">
    <h2><i class="bi bi-hospital"></i> Centro de Salud Sur</h2>
    <p>Inicie sesión para continuar</p>

    <form action="login.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

      <div class="mb-3 text-start">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <?php if (!empty($_GET['err'])): ?>
        <div class="alert alert-danger py-2">
          <?php
            if ($_GET['err'] === 'invalid') echo 'Usuario o contraseña inválidos.';
            elseif ($_GET['err'] === 'csrf') echo 'Token de seguridad inválido. Intente nuevamente.';
            elseif ($_GET['err'] === 'db') echo 'Error interno de conexión.';
          ?>
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-login w-100 text-white">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
      </button>
    </form>

    <div class="small-muted mt-3">
      <p>Centro de Salud Sur de Huehuetenango<br><small>Sistema de gestión médica</small></p>
    </div>
  </div>
</body>
</html>
