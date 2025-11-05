<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: listar.php'); exit; }

try {
    $stmt = $conexion->prepare("SELECT id, username, role FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) { header('Location: listar.php'); exit; }
} catch (PDOException $e) {
    die("❌ Error al cargar usuario: " . $e->getMessage());
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? 'usuario');

    if ($username === '') {
        $errors[] = "Usuario requerido.";
    } else {
        try {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("UPDATE usuarios SET username = ?, password = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $hash, $role, $id]);
            } else {
                $stmt = $conexion->prepare("UPDATE usuarios SET username = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $role, $id]);
            }
            header('Location: listar.php?msg=updated');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Error al actualizar: " . $e->getMessage();
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">
  <h3><i class="bi bi-pencil-fill me-2"></i>Editar usuario #<?= $user['id'] ?></h3>

  <?php if ($errors): foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; endif; ?>

  <form method="POST" autocomplete="off">
    <div class="mb-3">
      <input name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <input name="password" type="password" class="form-control" placeholder="Dejar en blanco para mantener">
    </div>
    <div class="mb-3">
      <select name="role" class="form-select">
        <option value="medico" <?= $user['role'] === 'medico' ? 'selected' : '' ?>>Médico</option>
        <option value="secretaria" <?= $user['role'] === 'secretaria' ? 'selected' : '' ?>>Secretaria</option>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
      </select>
    </div>
    <div>
      <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
      <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
