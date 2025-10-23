<?php
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // 🖥️ Conexión local
    $host = '127.0.0.1';
    $user = 'root';
    $password = '';
    $dbname = 'hospital_huehuetenango'; // cambia al nombre de tu DB local
    $port = 3306;
} else {
    // ☁️ Conexión en Railway
    $host = 'maglev.proxy.rlwy.net';
    $user = 'root';
    $password = 'gIEWCrtENrUZBedRLzuonZaLqaCHBnMC';
    $dbname = 'railway';
    $port = 45098;
}

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>