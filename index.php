<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

// Si hay sesión activa, redirige según el rol
if (is_logged_in()) {
    redirect_by_role($_SESSION['user']['rol']);
    exit;
}

// Si no hay sesión, mostrar el login
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Centro de Salud Sur - Inicio de Sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(180deg, #0090db 0%, #00bfff 100%);
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #333;
    }
    .login-card {
      background: #fff;
      padding: 2.5rem;
      border-radius: 1rem;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      max-width: 420px;
      width: 100%;
    }
    .login-card h3 {
      text-align: center;
      color: #0078b0;
      font-weight: 700;
    }
    .btn-primary {
      background-color: #0090db;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0078b0;
    }
    .form-label {
      font-weight: 500;
    }
    .small-muted {
      color: #777;
      font-size: 0.9rem;
      text-align: center;
      margin-top: 1rem;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h3><i class="bi bi-hospital"></i> Centro de Salud Sur</h3>
    <p class="text-center small-muted mb-4">Inicie sesión para continuar</p>

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
        <div class="alert alert-danger py-2">
          <?php
            if ($_GET['err'] === 'invalid') echo 'Usuario o contraseña inválidos.';
            elseif ($_GET['err'] === 'csrf') echo 'Token de seguridad inválido. Intente nuevamente.';
            elseif ($_GET['err'] === 'db') echo 'Error interno en la base de datos.';
          ?>
        </div>
      <?php endif; ?>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
        </button>
      </div>
    </form>

    <div class="small-muted mt-4">
      <p>Centro de Salud Sur de Huehuetenango<br>
      <small>Sistema de gestión médica</small></p>
    </div>
  </div>

</body>
</html>
