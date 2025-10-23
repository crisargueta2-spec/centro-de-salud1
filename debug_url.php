<?php
require_once __DIR__ . '/includes/config.php';
echo "APP_URL: " . APP_URL . "<br>";
echo "Redirigiendo a: " . APP_URL . "roles/admin_dashboard.php<br>";
header('Location: ' . APP_URL . 'roles/admin_dashboard.php');
exit;
?>