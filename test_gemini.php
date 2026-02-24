<?php
$apiKey = "AIzaSyDIyfZULPhyAOG9WIY3gSlHoTa42-7eR5s";
$url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['models'])) {
        foreach ($data['models'] as $model) {
            echo "- " . $model['name'] . " (" . implode(", ", $model['supportedGenerationMethods']) . ")\n";
        }
    } else {
        echo "No models found in response.\n";
        var_dump($data);
    }
} else {
    echo "Response: $response\n";
}
