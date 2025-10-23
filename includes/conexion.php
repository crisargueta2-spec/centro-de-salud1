<?php
// ============================================================
// 🔗 Conexión a MySQL en Railway (producción)
// ============================================================

$host = 'maglev.proxy.rlwy.net';
$user = 'root';
$password = 'gIEWCrtENrUZBedRLzuonZaLqaCHBnMC';
$dbname = 'railway';
$port = 45098;

try {
    // Conexión PDO
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);

    // Modo de errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Log interno para Railway (visible en Logs)
    error_log("✅ Conectado a la base de datos $dbname en $host:$port");
} catch (PDOException $e) {
    error_log("❌ Error de conexión DB: " . $e->getMessage());
    die("<p style='color:red'>Error de conexión a la base de datos.</p>");
}
?>
