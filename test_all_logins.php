<?php

// Test All User Logins
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTING ALL USER LOGINS\n";
echo "==========================\n\n";

$testUsers = [
    ['username' => 'admin', 'password' => 'admin123'],
    ['username' => 'daffa', 'password' => '12345678'],
    ['username' => 'testuser', 'password' => 'password']
];

foreach ($testUsers as $testUser) {
    $request = new Illuminate\Http\Request();
    $request->merge(['login' => $testUser['username'], 'password' => $testUser['password']]);
    $request->headers->set('Accept', 'application/json');

    $controller = new App\Http\Controllers\Api\AuthController();
    $response = $controller->login($request);

    $content = json_decode($response->getContent(), true);
    $status = $response->getStatusCode();

    if ($status === 200) {
        echo '✅ ' . $testUser['username'] . ' - LOGIN SUCCESS (Role: ' . ($content['role'] ?? 'N/A') . ')' . PHP_EOL;
    } else {
        echo '❌ ' . $testUser['username'] . ' - LOGIN FAILED (Status: ' . $status . ')' . PHP_EOL;
        if (isset($content['message'])) {
            echo '   Message: ' . $content['message'] . PHP_EOL;
        }
    }
}

echo "\n🎯 ALL LOGIN TESTS COMPLETED!\n";

?></content>
<parameter name="filePath">/home/daffarizky/Project Halalytics/Halalytics/test_all_logins.php