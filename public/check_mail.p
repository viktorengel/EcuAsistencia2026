<?php
// Sube este archivo a /public_html/check_mail.php y ábrelo en el navegador
// Luego elimínalo

echo "<h2>Diagnóstico de correo — EcuAsist</h2><pre>";

$functions = ['mail', 'fsockopen', 'curl_init', 'curl_exec', 'stream_socket_client', 'socket_create'];
foreach ($functions as $f) {
    $ok = function_exists($f) ? "✅ disponible" : "❌ bloqueada";
    echo str_pad($f . '()', 25) . " → $ok\n";
}

echo "\n--- PHP Version: " . phpversion() . "\n";
echo "--- Sendmail path: " . ini_get('sendmail_path') . "\n";
echo "--- SMTP: " . ini_get('SMTP') . "\n";
echo "--- smtp_port: " . ini_get('smtp_port') . "\n";
echo "--- disable_functions:\n";
$disabled = ini_get('disable_functions');
echo $disabled ? $disabled : "(ninguna)";
echo "\n</pre>";
echo "<p style='color:red;font-weight:bold;'>⚠️ Elimina este archivo después de revisar</p>";