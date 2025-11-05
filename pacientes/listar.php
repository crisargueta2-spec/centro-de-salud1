<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'secretaria', 'medico']); // permite a los tres roles
require_once __DIR__ . '/../includes/conexion.php';

try {
    // Filtros básicos
    $filtro = $_GET['filtro'] ?? 'todos';

    if ($filtro === 'hoy') {
        $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE DATE(fecha_referencia) = CURDATE() ORDER BY id DESC");
        $stmt->execute();
    } elseif ($filtro === 'semana') {
        $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE YEARWEEK(fecha_referencia, 1) = YEARWEEK(CURDATE(), 1) ORDER BY id DESC");
        $stmt->execute();
    } else {
        $stmt = $conexion->query("SELECT * FROM pacientes ORDER BY id DESC");
    }

    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Error al cargar pacientes: " . $e->getMessage());
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">

  <!-- 🔍 ENCABEZADO SUPERIOR -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-person-lines-fill me-2"></i>Pacientes</h3>
    <div class="d-flex">
      <form class="me-2" method="GET" action="">
        <input type="text" name="buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" 
               class="form-control" placeholder="Buscar...">
      </form>
      <?php if (user()['rol'] === 'admin' || user()['rol'] === 'secretaria'): ?>
        <a href="../pacientes/crear.php" class="btn btn-success">
          <i class="bi bi-plus-lg"></i> Crear paciente
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- 📅 FILTROS POR FECHA -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= $filtro === 'todos' ? 'active' : '' ?>" href="?filtro=todos">Todos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $filtro === 'hoy' ? 'active' : '' ?>" href="?filtro=hoy">Hoy</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $filtro === 'semana' ? 'active' : '' ?>" href="?filtro=semana">Esta semana</a>
    </li>
  </ul>

  <!-- 📋 TABLA DE PACIENTES -->
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Fecha Nac.</th>
          <th>Género</th>
          <th>Motivo</th>
          <th>Médico Referente</th>
          <th>Fecha Ref.</th>
          <?php if (user()['rol'] === 'admin' || user()['rol'] === 'secretaria'): ?>
            <th>Acciones</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php
        $buscar = strtolower(trim($_GET['buscar'] ?? ''));
        $hayResultados = false;

        foreach ($pacientes as $p):
          $texto = strtolower($p['nombre'] . ' ' . $p['apellido'] . ' ' . $p['motivo']);
          if ($buscar && !str_contains($texto, $buscar)) continue;
          $hayResultados = true;
        ?>
          <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['apellido']) ?></td>
            <td><?= htmlspecialchars($p['fecha_nacimiento']) ?></td>
            <td><?= htmlspecialchars($p['genero']) ?></td>
            <td><?= htmlspecialchars($p['motivo']) ?></td>
            <td><?= htmlspecialchars($p['medico_referente']) ?></td>
            <td><?= htmlspecialchars($p['fecha_referencia']) ?></td>
            <?php if (user()['rol'] === 'admin' || user()['rol'] === 'secretaria'): ?>
              <td>
                <a href="../pacientes/editar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="../pacientes/eliminar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('¿Eliminar paciente <?= htmlspecialchars($p['nombre']) ?> <?= htmlspecialchars($p['apellido']) ?>?')">
                   <i class="bi bi-trash"></i>
                </a>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>

        <?php if (!$hayResultados): ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-3">No se encontraron pacientes</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
