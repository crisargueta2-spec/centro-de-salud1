<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? '';
    $especialista_id = $_POST['especialista_id'] ?? '';
    $fecha_cita = $_POST['fecha_cita'] ?? '';
    $prioridad = $_POST['prioridad'] ?? '';

    // Insertar asignación en la tabla
    $stmt = $conn->prepare("INSERT INTO asignaciones (paciente_id, especialista_id, fecha_cita, prioridad) VALUES (?, ?, ?, ?)");
    $stmt->execute([$paciente_id, $especialista_id, $fecha_cita, $prioridad]);

    $mensaje = "Asignación registrada correctamente.";
}

// Obtener lista de pacientes para el dropdown
$pacientes = $conn->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de especialistas para el dropdown
$especialistas = $conn->query("SELECT id, nombre, especialidad FROM especialistas ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Asignar Especialista</h2>

<?php if (isset($mensaje)) echo "<div class='alert alert-success'>$mensaje</div>"; ?>

<form method="POST" action="">
    <div class="mb-3">
        <label for="paciente_id" class="form-label">Paciente:</label>
        <select id="paciente_id" name="paciente_id" class="form-select" required>
            <option value="">Seleccione paciente</option>
            <?php foreach ($pacientes as $paciente): ?>
                <option value="<?= $paciente['id'] ?>">
                    <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="especialista_id" class="form-label">Especialista:</label>
        <select id="especialista_id" name="especialista_id" class="form-select" required>
            <option value="">Seleccione especialista</option>
            <?php foreach ($especialistas as $especialista): ?>
                <option value="<?= $especialista['id'] ?>">
                    <?= htmlspecialchars($especialista['nombre'] . ' (' . $especialista['especialidad'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
        <input type="date" id="fecha_cita" name="fecha_cita" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="prioridad" class="form-label">Prioridad:</label>
        <select id="prioridad" name="prioridad" class="form-select" required>
            <option value="">Seleccione prioridad</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Asignación</button>
</form>

<?php include '../templates/footer.php'; ?>
