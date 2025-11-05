<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'secretaria', 'medico']);
include __DIR__ . '/../templates/header.php';
?>

<div class="page-head mb-4">
  <h1 class="h3 mb-0">Gestión de Pacientes</h1>
  <p class="text-muted">Consulta, filtra o administra los registros de pacientes.</p>
</div>

<!-- 🔹 Filtros de búsqueda -->
<div class="d-flex align-items-center flex-wrap gap-2 mb-4">
  <label class="form-label mb-0 me-2 fw-semibold">Ver por día/mes:</label>
  <input type="date" id="fecha_dia" class="form-control w-auto" value="<?= date('Y-m-d') ?>">
  <button class="btn btn-secondary" id="btnVerDia">Ver día</button>

  <input type="month" id="fecha_mes" class="form-control w-auto" value="<?= date('Y-m') ?>">
  <button class="btn btn-secondary" id="btnVerMes">Ver mes</button>

  <button class="btn btn-outline-dark" id="btnHoy">Hoy</button>
  <button class="btn btn-outline-dark" id="btnTodos">Todos</button>
</div>

<!-- 🔹 Tabla de pacientes -->
<div class="table-responsive shadow-sm rounded bg-white p-3">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>DPI</th>
        <th>Edad</th>
        <th>Teléfono</th>
        <th>Fecha de Registro</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php
      require_once __DIR__ . '/../includes/conexion.php';
      try {
          $query = "SELECT id, nombre, dpi, edad, telefono, fecha_registro FROM pacientes ORDER BY fecha_registro DESC";
          $stmt = $conexion->query($query);
          $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($pacientes) === 0) {
              echo "<tr><td colspan='7' class='text-center text-muted py-3'>No hay pacientes registrados.</td></tr>";
          } else {
              foreach ($pacientes as $p) {
                  echo "<tr>
                          <td>{$p['id']}</td>
                          <td>" . htmlspecialchars($p['nombre']) . "</td>
                          <td>{$p['dpi']}</td>
                          <td>{$p['edad']}</td>
                          <td>{$p['telefono']}</td>
                          <td>{$p['fecha_registro']}</td>
                          <td>
                            <a href='editar.php?id={$p['id']}' class='btn btn-sm btn-outline-primary'>
                              <i class='bi bi-pencil-square'></i>
                            </a>
                            <a href='eliminar.php?id={$p['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"¿Eliminar paciente?\")'>
                              <i class='bi bi-trash'></i>
                            </a>
                          </td>
                        </tr>";
              }
          }
      } catch (PDOException $e) {
          echo "<tr><td colspan='7' class='text-danger text-center'>Error al cargar pacientes: {$e->getMessage()}</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- 🔹 Script para manejo de botones -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const fechaDia = document.getElementById('fecha_dia');
  const fechaMes = document.getElementById('fecha_mes');
  const btnVerDia = document.getElementById('btnVerDia');
  const btnVerMes = document.getElementById('btnVerMes');
  const btnHoy = document.getElementById('btnHoy');
  const btnTodos = document.getElementById('btnTodos');

  btnVerDia?.addEventListener('click', () => {
    const fecha = fechaDia.value;
    if (fecha) window.location = `listar.php?tipo=dia&fecha=${fecha}`;
  });

  btnVerMes?.addEventListener('click', () => {
    const fecha = fechaMes.value;
    if (fecha) window.location = `listar.php?tipo=mes&fecha=${fecha}`;
  });

  btnHoy?.addEventListener('click', () => {
    const hoy = new Date().toISOString().split('T')[0];
    window.location = `listar.php?tipo=dia&fecha=${hoy}`;
  });

  btnTodos?.addEventListener('click', () => {
    window.location = 'listar.php';
  });
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>

