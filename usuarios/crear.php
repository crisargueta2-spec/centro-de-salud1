<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/conexion.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? 'usuario');

    if ($username === '' || $password === '') {
        $errors[] = "Usuario y contraseña son obligatorios.";
    } else {
        try {
            // Verificar si el usuario ya existe
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
            $check->execute([$username]);

            if ($check->fetch()) {
                $errors[] = "El usuario '$username' ya existe.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hash, $role]);
                header('Location: listar.php?msg=created');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Error al crear usuario: " . $e->getMessage();
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">
  <h3><i class="bi bi-person-plus-fill me-2"></i>Crear usuario</h3>

  <?php if ($errors): foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; endif; ?>

  <form method="POST" autocomplete="off">
    <div class="mb-3">
      <input name="username" class="form-control" placeholder="Usuario" required>
    </div>
    <div class="mb-3">
      <input name="password" type="password" class="form-control" placeholder="Contraseña" required>
    </div>
    <div class="mb-3">
      <select name="role" class="form-select">
        <option value="medico">Médico</option>
        <option value="secretaria">Secretaria</option>
        <option value="admin">Administrador</option>
      </select>
    </div>
    <div>
      <button class="btn btn-success"><i class="bi bi-check-lg"></i> Crear</button>
      <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
