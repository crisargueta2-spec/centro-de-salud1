<?php
// $servername = "localhost";  // Normalmente localhost si usas XAMPP u otro servidor local
/*$username = "root";         // Usuario de tu base de datos (por defecto root)
$password = "";             // Contraseña (por defecto vacía en XAMPP)
$dbname = "hospital_huehuetenango";  // Nombre exacto de la base de datos que creaste

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Configura para que lance excepciones en errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_huehuetenango"; // Asegúrate de que este sea el nombre correcto de tu base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
