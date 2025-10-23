<?php
// 🔒 Forzar HTTPS siempre (Railway usa SSL por defecto)
$protocol = 'https';
define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
?>
