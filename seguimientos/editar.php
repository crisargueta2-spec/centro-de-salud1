<?php
include '../includes/conexion.php';

// Verificar que se ha pasado el ID del seguimiento a editar
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $paciente_id = $_POST['paciente_id'];
    $resultado = $_POST['resultado'];
    $proxima_cita = $_POST['proxima_cita'];

    // Actualizar el seguimiento en la base de datos
    $stmt = $conn->prepare("UPDATE seguimientos SET paciente_id = ?, resultado = ?, proxima_cita = ? WHERE id = ?");
    $stmt->execute([$paciente_id, $resultado, $proxima_cita, $id]);

    $mensaje = "Seguimiento actualizado correctamente.";
}

// Obtener los datos del seguimiento a editar
$stmt = $conn->prepare("SELECT * FROM seguimientos WHERE id = ?");
$stmt->execute([$id]);
$seguimiento = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener lista de pacientes para el dropdown
$pacientes = $conn->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Editar Seguimiento Clínico</h2>

<?php if (isset($mensaje)) echo "<div class='alert alert-success'>$mensaje</div>"; ?>

<form method="POST" action="">
    <input type="hidden" name="id" value="<?= $seguimiento['id'] ?>">

    <!-- Paciente -->
    <div class="mb-3">
        <label for="paciente_id" class="form-label">Paciente:</label>
        <select id="paciente_id" name="paciente_id" class="form-select" required>
            <option value="">Seleccione paciente</option>
            <?php foreach ($pacientes as $paciente): ?>
                <option value="<?= $paciente['id'] ?>" <?= $seguimiento['paciente_id'] == $paciente['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Resultado -->
    <div class="mb-3">
        <label for="resultado" class="form-label">Resultado de la Consulta:</label>
        <textarea id="resultado" name="resultado" class="form-control" rows="4" required><?= htmlspecialchars($seguimiento['resultado']) ?></textarea>
    </div>

    <!-- Próxima cita -->
    <div class="mb-3">
        <label for="proxima_cita" class="form-label">Próxima Cita:</label>
        <input type="date" id="proxima_cita" name="proxima_cita" class="form-control" value="<?= $seguimiento['proxima_cita'] ?>" />
    </div>

    <button type="submit" class="btn btn-primary">Actualizar Seguimiento</button>
</form>

<?php include '../templates/footer.php'; ?>
