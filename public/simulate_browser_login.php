<?php
// Simulation eines Browser-Logins via cURL

$baseUrl = 'http://localhost/Webshop/public';
$cookieFile = __DIR__ . '/cookie_jar.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function request($url, $method = 'GET', $postData = []) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    return ['body' => $response, 'info' => $info];
}

echo "1. Rufe Login-Seite auf...\n";
$res = request("$baseUrl/anmelden");

// Extrahiere CSRF Token
if (preg_match('/name="_csrf_token" value="([^"]+)"/', $res['body'], $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token gefunden: $csrfToken\n";
} else {
    die("FAILED: Kein CSRF Token gefunden.\n");
}

echo "\n2. Sende Login-Formular (Max Mustermann)...\n";
$res = request("$baseUrl/anmelden", 'POST', [
    '_csrf_token' => $csrfToken,
    'email' => 'max.mustermann@email.de',
    'password' => 'user123'
]);

echo "   HTTP Code: " . $res['info']['http_code'] . "\n";
echo "   Effective URL: " . $res['info']['url'] . "\n";

// Prüfe auf Erfolg
if (strpos($res['body'], 'Max Mustermann') !== false) {
    echo "\nSUCCESS: 'Max Mustermann' wurde auf der Zielseite gefunden!\n";
    echo "Login erfolgreich.\n";
} elseif (strpos($res['body'], 'Willkommen zurück') !== false) {
    echo "\nSUCCESS: 'Willkommen zurück' Nachricht gefunden.\n";
} else {
    echo "\nFAILED: Login scheint fehlgeschlagen zu sein. 'Max Mustermann' nicht gefunden.\n";
    // Debug: Zeige Fehler-Flash
    if (preg_match('/div class=".*bg-red-100.*">(.*?)<\/div>/s', $res['body'], $err)) {
        echo "Fehlermeldung auf Seite: " . trim(strip_tags($err[1])) . "\n";
    }
}

// Cleanup
if (file_exists($cookieFile)) unlink($cookieFile);
