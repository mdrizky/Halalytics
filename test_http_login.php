<?php

// HTTP Login Test Simulation
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTING LOGIN VIA HTTP SIMULATION\n";
echo "=====================================\n\n";

// Simulate HTTP request to login
$request = new Illuminate\Http\Request();
$request->merge(['login' => 'admin', 'password' => 'admin123']);
$request->headers->set('Accept', 'application/json');
$request->headers->set('Content-Type', 'application/json');

$controller = new App\Http\Controllers\Api\AuthController();
$response = $controller->login($request);

echo 'Status Code: ' . $response->getStatusCode() . PHP_EOL;
$content = json_decode($response->getContent(), true);

if ($content) {
    echo 'Response: ' . json_encode($content, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
} else {
    echo 'Raw Response: ' . $response->getContent() . PHP_EOL . PHP_EOL;
}

if ($response->getStatusCode() === 200) {
    echo "🎉 LOGIN SUCCESS! System is working perfectly.\n";
    echo "\n📋 Login Details:\n";
    echo "   User: " . ($content['user']['username'] ?? 'N/A') . "\n";
    echo "   Role: " . ($content['role'] ?? 'N/A') . "\n";
    echo "   Token: " . substr($content['token'] ?? '', 0, 20) . "...\n";
} else {
    echo "❌ LOGIN FAILED! Check the response above.\n";
    if (isset($content['errors'])) {
        echo "Errors: " . json_encode($content['errors']) . "\n";
    }
}

echo "\n🔧 Test completed at: " . date('Y-m-d H:i:s') . "\n";

?></content>
<parameter name="filePath">/home/daffarizky/Project Halalytics/Halalytics/test_http_login.php