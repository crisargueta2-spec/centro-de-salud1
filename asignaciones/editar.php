<?php
include '../includes/conexion.php';  // Incluye la conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id = $_POST['id'];
    $paciente_id = $_POST['paciente_id'];
    $especialista_id = $_POST['especialista_id'];
    $fecha_cita = $_POST['fecha_cita'];
    $prioridad = $_POST['prioridad'];

    // Actualizar la asignación en la base de datos
    $sql = "UPDATE asignaciones SET paciente_id=?, especialista_id=?, fecha_cita=?, prioridad=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$paciente_id, $especialista_id, $fecha_cita, $prioridad, $id]);

    // Redirigir al listado de asignaciones
    header("Location: listar.php");  
    exit();
}

// Obtener los datos de la asignación a editar
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM asignaciones WHERE id=?");
$stmt->execute([$id]);
$asignacion = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener el nombre del paciente (nombre y apellido)
$pacienteStmt = $conn->prepare("SELECT nombre, apellido FROM pacientes WHERE id=?");  // Utilizamos 'apellido' aquí
$pacienteStmt->execute([$asignacion['paciente_id']]);
$paciente = $pacienteStmt->fetch(PDO::FETCH_ASSOC);

// Obtener el nombre del especialista
$especialistaStmt = $conn->prepare("SELECT nombre FROM especialistas WHERE id=?");  // Solo 'nombre' para el especialista
$especialistaStmt->execute([$asignacion['especialista_id']]);
$especialista = $especialistaStmt->fetch(PDO::FETCH_ASSOC);

include '../templates/header.php';  // Incluye el encabezado
?>

<h2>Editar Asignación</h2>

<form method="POST" action="">
    <input type="hidden" name="id" value="<?= $asignacion['id'] ?>">

    <!-- Mostrar nombre del paciente (nombre y apellido) -->
    <div class="mb-3">
        <label for="paciente_id" class="form-label">Paciente:</label>
        <input type="text" class="form-control" name="paciente_id" value="<?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?>" disabled>
    </div>

    <!-- Mostrar nombre del especialista -->
    <div class="mb-3">
        <label for="especialista_id" class="form-label">Especialista:</label>
        <input type="text" class="form-control" name="especialista_id" value="<?= htmlspecialchars($especialista['nombre']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label for="fecha_cita" class="form-label">Fecha de Cita:</label>
        <input type="date" class="form-control" name="fecha_cita" value="<?= $asignacion['fecha_cita'] ?>" required>
    </div>

    <div class="mb-3">
        <label for="prioridad" class="form-label">Prioridad:</label>
        <select name="prioridad" class="form-control" required>
            <option value="alta" <?= $asignacion['prioridad'] == 'alta' ? 'selected' : '' ?>>Alta</option>
            <option value="media" <?= $asignacion['prioridad'] == 'media' ? 'selected' : '' ?>>Media</option>
            <option value="baja" <?= $asignacion['prioridad'] == 'baja' ? 'selected' : '' ?>>Baja</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar Asignación</button>
</form>

<?php include '../templates/footer.php'; ?>
