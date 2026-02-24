<?php
/**
 * sync_user.php - Bridge for Firebase Auth and MySQL
 * Using Upsert logic (ON DUPLICATE KEY UPDATE)
 */

header('Content-Type: application/json');

// Configuration - Usually these should be in a separate config or .env file
$host = 'localhost';
$db   = 'halalytics';
$user = 'root';
$pass = ''; // Set your database password here
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERR_MODE            => PDO::ERR_MODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Get POST data
$firebase_uid = $_POST['firebase_uid'] ?? null;
$email = $_POST['email'] ?? null;
$display_name = $_POST['display_name'] ?? null;
$fcm_token = $_POST['fcm_token'] ?? null;

if (!$firebase_uid || !$email) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: firebase_uid and email are mandatory.']);
    exit;
}

try {
    // Upsert Logic
    $sql = "INSERT INTO users (firebase_uid, email, display_name, fcm_token) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            email = VALUES(email), 
            display_name = VALUES(display_name), 
            fcm_token = VALUES(fcm_token),
            last_sync = CURRENT_TIMESTAMP";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$firebase_uid, $email, $display_name, $fcm_token]);

    echo json_encode([
        'success' => true, 
        'message' => 'User sync successful',
        'data' => [
            'firebase_uid' => $firebase_uid,
            'last_sync' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Execution error: ' . $e->getMessage()]);
}
?>
