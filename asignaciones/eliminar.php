<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
require_once __DIR__.'/../includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        http_response_code(400); exit('CSRF');
    }

    $del = $conexion->prepare("DELETE FROM asignaciones WHERE id=?");
    $del->execute([$id]);

    header("Location: listar.php?ok=3"); exit;
}

include __DIR__.'/../templates/header.php';
?>

<div class="container py-3">
  <h3>Eliminar</h3>
  <p>¿Eliminar asignación #<?= $id ?>?</p>

  <form method="POST">
    <?php csrf_field(); ?>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    <button class="btn btn-danger">Eliminar</button>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
