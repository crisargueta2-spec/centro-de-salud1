<?php
include '../includes/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: listar.php');
    exit;
}

// Obtener datos del paciente
$stmt = $conn->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    echo "Paciente no encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $genero = $_POST['genero'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $medico_referente = $_POST['medico_referente'] ?? '';

    $stmt = $conn->prepare("UPDATE pacientes SET nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, motivo = ?, medico_referente = ? WHERE id = ?");
    $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $genero, $motivo, $medico_referente, $id]);

    header('Location: listar.php');
    exit;
}

include '../templates/header.php';
?>

<h2>Editar Paciente</h2>

<form method="POST" action="">
    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($paciente['nombre']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($paciente['apellido']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($paciente['fecha_nacimiento']) ?>" class="form-control">
    </div>
    <div class="mb-3">
        <label>Género:</label>
        <select name="genero" class="form-select" required>
            <option value="M" <?= $paciente['genero'] === 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= $paciente['genero'] === 'F' ? 'selected' : '' ?>>Femenino</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Motivo de la Referenciación:</label>
        <textarea name="motivo" class="form-control" required><?= htmlspecialchars($paciente['motivo']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Médico que Refiere:</label>
        <input type="text" name="medico_referente" value="<?= htmlspecialchars($paciente['medico_referente']) ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../templates/footer.php'; ?>
