<?php
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/csrf.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
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
  <title>Login</title>
  <link href="assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
          <h5>Centro de Salud Sur</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="POST">
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
    </div>
  </div>
</div>
</body>
</html>


