<?php
session_start();
require 'config.php'; // Include your database configuration file

// Function to send a JSON response
function sendResponse($success, $message = '', $api_key = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'api_key' => $api_key
    ]);
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email from the form
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if ($email) {
        try {
            // Prepare a statement to retrieve the API key for the given email
            $stmt = $pdo->prepare("SELECT api_key FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);

            // Fetch the API key
            $result = $stmt->fetch();

            if ($result) {
                // If the API key exists, return it
                sendResponse(true, 'API key retrieved successfully.', $result['api_key']);
            } else {
                // If no API key found for the email
                sendResponse(false, 'No API key found for this email.');
            }
        } catch (PDOException $e) {
            sendResponse(false, 'Database error: ' . $e->getMessage());
        }
    } else {
        sendResponse(false, 'Invalid email provided.');
    }
} else {
    sendResponse(false, 'Invalid request method.');
}
