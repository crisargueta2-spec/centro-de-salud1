<?php
$host = "maglev.proxy.rlwy.net";
$user = "root";
$password = "gIEWCrtENrUZBedRLzuonZaLqaCHBnMC";
$dbname = "railway";
$port = 45098;

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
