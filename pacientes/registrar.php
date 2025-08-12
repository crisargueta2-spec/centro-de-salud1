<?php
include '../includes/conexion.php';  // Incluye conexión a BD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $fecha_nacimiento = $_POST['fechaNacimiento'] ?? null;
    $genero = $_POST['genero'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $medico_referente = $_POST['medico'] ?? '';

    // Preparar y ejecutar inserción segura
    $stmt = $conn->prepare("INSERT INTO pacientes (nombre, apellido, fecha_nacimiento, genero, motivo, medico_referente) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $genero, $motivo, $medico_referente]);

    $mensaje = "Paciente registrado correctamente.";
}

include '../templates/header.php';  // Encabezado HTML
?>

<h2>Registrar Paciente</h2>

<?php if (isset($mensaje)) echo "<div class='alert alert-success'>$mensaje</div>"; ?>

<form method="POST" action="">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Apellido:</label><br>
    <input type="text" name="apellido" required><br><br>

    <label>Fecha de Nacimiento:</label><br>
    <input type="date" name="fechaNacimiento"><br><br>

    <label>Género:</label><br>
    <select name="genero" required>
        <option value="">Seleccione</option>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
    </select><br><br>

    <label>Motivo de la Referenciación:</label><br>
    <textarea name="motivo" required></textarea><br><br>

    <label>Médico que Refiere:</label><br>
    <input type="text" name="medico" required><br><br>

    <button type="submit">Registrar</button>
</form>

<?php include '../templates/footer.php';  // Pie de página ?>
