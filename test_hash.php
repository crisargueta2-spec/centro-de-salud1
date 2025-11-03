<?php
$hash = '$2y$10$e0NR7KJpO9ztW0OZcUeEdeBpvFP4UGpLX7V14J0rLrqu9g8Zp5Uj2'; // hash del admin actual

if (password_verify('1234', $hash)) {
    echo "✅ La contraseña 1234 es válida para este hash.";
} else {
    echo "❌ La contraseña 1234 NO coincide con este hash.";
}
?>
