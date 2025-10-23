<?php
// Configuración base para entorno de producción en Railway

// Ruta base relativa (la raíz del proyecto)
define('APP_BASE', '/');

// URL base absoluta (Railway asigna un dominio público)
define('APP_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');

// Ejemplo: si Railway asigna "https://centro-de-salud1-production.up.railway.app"
// el APP_URL resultará automáticamente correcto.
?>