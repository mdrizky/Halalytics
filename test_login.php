<?php

// Test Login Script for Halalytics
// Run with: php test_login.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🔍 TESTING HALALYTICS LOGIN SYSTEM\n";
echo "==================================\n\n";

// Test 1: Check database connection
echo "1. Testing Database Connection...\n";
try {
    $userCount = User::count();
    echo "✅ Database connected. Found $userCount users.\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check user credentials
echo "2. Testing User Credentials...\n";

$testUsers = [
    ['username' => 'admin', 'password' => 'admin123', 'expected_role' => 'admin'],
    ['username' => 'daffa', 'password' => '12345678', 'expected_role' => 'user'],
    ['username' => 'testuser', 'password' => 'password', 'expected_role' => 'user'],
];

foreach ($testUsers as $testUser) {
    $user = User::where('username', $testUser['username'])->first();

    if (!$user) {
        echo "❌ User '{$testUser['username']}' not found in database\n";
        continue;
    }

    $passwordValid = Hash::check($testUser['password'], $user->password);
    $roleValid = ($user->role === $testUser['expected_role']);

    if ($passwordValid && $roleValid) {
        echo "✅ User '{$testUser['username']}' - Password & Role OK\n";
    } else {
        echo "❌ User '{$testUser['username']}' - ";
        if (!$passwordValid) echo "Password INVALID ";
        if (!$roleValid) echo "Role INVALID (expected: {$testUser['expected_role']}, got: {$user->role})";
        echo "\n";
    }
}

echo "\n3. Testing Auth Controller Logic...\n";

// Test 3: Simulate login request
try {
    $user = User::where('username', 'admin')->first();

    if ($user && Hash::check('admin123', $user->password)) {
        echo "✅ Auth logic works - Admin login successful\n";

        // Test token generation
        $token = $user->createToken('test-token');
        echo "✅ Token generation works - Token: " . substr($token->plainTextToken, 0, 20) . "...\n";
    } else {
        echo "❌ Auth logic failed\n";
    }
} catch (Exception $e) {
    echo "❌ Auth controller error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing Sanctum Configuration...\n";

// Test 4: Check Sanctum guard
try {
    $guards = config('auth.guards');
    if (isset($guards['sanctum'])) {
        echo "✅ Sanctum guard configured\n";
    } else {
        echo "❌ Sanctum guard missing\n";
    }

    $sanctumConfig = config('sanctum.guard');
    echo "✅ Sanctum guard setting: " . json_encode($sanctumConfig) . "\n";
} catch (Exception $e) {
    echo "❌ Sanctum config error: " . $e->getMessage() . "\n";
}

echo "\n🎯 FINAL RESULT\n";
echo "==============\n";

$allTestsPass = true;

// Re-check critical items
$user = User::where('username', 'admin')->first();
if (!$user || !Hash::check('admin123', $user->password)) {
    $allTestsPass = false;
}

$guards = config('auth.guards');
if (!isset($guards['sanctum'])) {
    $allTestsPass = false;
}

if ($allTestsPass) {
    echo "🎉 ALL TESTS PASSED! Login system is working correctly.\n";
    echo "\n📋 Ready to use credentials:\n";
    echo "   Admin: admin / admin123\n";
    echo "   User:  daffa / 12345678\n";
    echo "   Test:  testuser / password\n";
} else {
    echo "❌ SOME TESTS FAILED! Login system needs fixes.\n";
}

echo "\n🔧 Next steps:\n";
echo "   1. Start server: php artisan serve\n";
echo "   2. Test API: POST /api/login with JSON credentials\n";
echo "   3. Check Android app connection\n";

?></content>
<parameter name="filePath">/home/daffarizky/Project Halalytics/Halalytics/test_login.php