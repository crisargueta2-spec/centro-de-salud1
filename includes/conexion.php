<?php
// 📦 Configuración dinámica: si estás en localhost usa local, si no, usa Railway
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // 🖥️ Conexión local
    $host = '127.0.0.1';
    $user = 'root';
    $password = '';
    $dbname = 'hospital_huehuetenango';
    $port = 3306;
} else {
    // ☁️ Conexión Railway
    $host = 'maglev.proxy.rlwy.net';
    $user = 'root';
    $password = 'gIEWCrtENrUZBedRLzuonZaLqaCHBnMC';
    $dbname = 'railway';
    $port = 45098;
}

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("✅ Conectado a la base de datos $dbname en $host:$port");
} catch (PDOException $e) {
    error_log("❌ Error de conexión DB: " . $e->getMessage());
    die("Error de conexión a la base de datos");
}
?>
