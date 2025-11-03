<?php
ob_start();

echo "<h2>🧩 Diagnóstico interno del admin_dashboard</h2>";

try {
    echo "1️⃣ Incluyendo auth.php...<br>";
    require_once __DIR__ . '/includes/auth.php';
    echo "✅ auth.php cargado.<br>";

    echo "2️⃣ Probando función require_role('admin')...<br>";
    require_role('admin');
    echo "✅ require_role ejecutado correctamente.<br>";

    echo "3️⃣ Probando user()...<br>";
    $u = user();
    echo "✅ user(): ";
    echo "<pre>"; print_r($u); echo "</pre>";

    echo "4️⃣ Incluyendo templates/header.php...<br>";
    include __DIR__ . '/templates/header.php';
    echo "✅ header incluido.<br>";

    echo "5️⃣ Incluyendo roles/admin_dashboard.php...<br>";
    include __DIR__ . '/roles/admin_dashboard.php';
    echo "✅ admin_dashboard.php completado.<br>";

    echo "6️⃣ Incluyendo templates/footer.php...<br>";
    include __DIR__ . '/templates/footer.php';
    echo "✅ footer incluido.<br>";

} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error detectado: " . $e->getMessage() . "</p>";
}

ob_end_flush();
?>
