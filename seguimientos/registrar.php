<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? '';
    $resultado = $_POST['resultado'] ?? '';
    $proxima_cita = $_POST['proxima_cita'] ?? null;

    $stmt = $conn->prepare("INSERT INTO seguimientos (paciente_id, resultado, proxima_cita) VALUES (?, ?, ?)");
    $stmt->execute([$paciente_id, $resultado, $proxima_cita]);

    $mensaje = "Seguimiento registrado correctamente.";
}

// Obtener lista de pacientes para el dropdown
$pacientes = $conn->query("SELECT id, nombre, apellido FROM pacientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Registrar Seguimiento Clínico</h2>

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
        <label for="resultado" class="form-label">Resultado de la Consulta:</label>
        <textarea id="resultado" name="resultado" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
        <label for="proxima_cita" class="form-label">Próxima Cita:</label>
        <input type="date" id="proxima_cita" name="proxima_cita" class="form-control" />
    </div>

    <button type="submit" class="btn btn-primary">Registrar Seguimiento</button>
</form>

<?php include '../templates/footer.php'; ?>
