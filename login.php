<?php
require_once __DIR__.'/includes/conexion.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Debe ingresar usuario y contraseña.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Centro de Salud Sur</title>
  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <div class="login-left">
      <h2>Centro de Salud Sur<br>de Huehuetenango</h2>
      <p>Sistema de gestión médica interna</p>
    </div>
    <div class="login-right">
      <h3>Iniciar Sesión</h3>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <?php csrf_field(); ?>
        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100">Ingresar</button>
      </form>
    </div>
  </div>
</body>
</html>


