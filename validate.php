<?php
session_start();
require_once 'config.php'; // Include the database connection file

// Initialize an array for response messages
$response = [
    'success' => false,
    'message' => ''
];

// Check if API key and user credentials are provided
if (isset($_POST['api_key']) && isset($_POST['email']) && isset($_POST['password'])) {
    $apiKey = $_POST['api_key'];
    $email = $_POST['email'];
    $password = $_POST['password']; // No need to hash here for verification

    // Validate API key
    $query = "SELECT * FROM users WHERE api_key = :api_key";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['api_key' => $apiKey]);
    $user = $stmt->fetch();

    if ($user) {
        // Check if the email exists
        $query = "SELECT * FROM app_user WHERE email = :email LIMIT 1"; // Limit to 1 record
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $appuser = $stmt->fetch();

        if ($appuser) {
            // Compare the provided password with the stored hashed password
            if (password_verify($password, $appuser['password'])) {
                $response['success'] = true;
                $response['message'] = "User validated successfully.";
            } else {
                $response['message'] = "Invalid email or password.";
            }
        } else {
            $response['message'] = "User with this email does not exist.";
        }
    } else {
        $response['message'] = "Invalid API key.";
    }
} else {
    $response['message'] = "API key, email, and password are required.";
}

// Set the content type to application/json
header('Content-Type: application/json');
echo json_encode($response);
exit;