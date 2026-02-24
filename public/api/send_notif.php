<?php
/**
 * send_notif.php - Send push notification via FCM HTTP v1 API
 * This script requires a service-account.json from Firebase Console
 */

require_once __DIR__ . '/../../vendor/autoload.php'; // Path to google-auth or firebase-php if used

use Google\Client;

function getAccessToken($serviceAccountPath) {
    $client = new Client();
    $client->setAuthConfig($serviceAccountPath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $token = $client->fetchAccessTokenWithAssertion();
    return $token['access_token'];
}

// Configuration
$project_id = 'halalytics-355ea'; // Ganti dengan Project ID Firebase kamu
$service_account_path = __DIR__ . '/../../service-account.json'; // Path ke file JSON Private Key

// Get POST data
$target_uid = $_POST['firebase_uid'] ?? null;
$notif_title = $_POST['title'] ?? 'Halalytics Alert';
$notif_body = $_POST['body'] ?? 'Informasi produk baru tersedia.';

if (!$target_uid) {
    die(json_encode(['success' => false, 'message' => 'Target Firebase UID is required']));
}

// 1. Ambil fcm_token dari MySQL
$pdo = new PDO("mysql:host=localhost;dbname=halalytics", "root", ""); 
$stmt = $pdo->prepare("SELECT fcm_token FROM users WHERE firebase_uid = ?");
$stmt->execute([$target_uid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !$row['fcm_token']) {
    die(json_encode(['success' => false, 'message' => 'FCM token not found for this user']));
}

$fcm_token = $row['fcm_token'];

// 2. Kirim Notifikasi via FCM HTTP v1
try {
    $access_token = getAccessToken($service_account_path);
    
    $url = "https://fcm.googleapis.com/v1/projects/$project_id/messages:send";
    
    $message = [
        'message' => [
            'token' => $fcm_token,
            'notification' => [
                'title' => $notif_title,
                'body' => $notif_body
            ],
            'data' => [
                'title' => $notif_title,
                'body' => $notif_body
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo json_encode([
        'success' => $status_code == 200,
        'fcm_response' => json_decode($result),
        'status_code' => $status_code
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
