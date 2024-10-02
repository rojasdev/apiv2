<?php
include 'config.php';  // Database connection

// Extract API key from headers
$api_key = $_POST['api_key'] ?? null;

if (!$api_key) {
    echo json_encode(['message' => 'API key missing']);
    exit();
}

// Validate the API key and get the user ID
$stmt = $pdo->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->execute([$api_key]);
$api_key_record = $stmt->fetch();

if (!$api_key_record) {
    echo json_encode(['message' => 'Invalid API key']);
    exit();
}

$user_id = $api_key_record['id'];

// Retrieve users created by this user
$stmt = $pdo->prepare("SELECT id, last_name, first_name, email, created_at FROM app_user WHERE user_id = ?");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output the result in JSON format
echo json_encode([
    'message' => 'User list retrieved successfully',
    'users' => $users
]);
?>
