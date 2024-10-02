<?php
session_start();
header('Content-Type: application/json'); // Set header for JSON response
require_once 'config.php'; // Include your database connection

// Function to check if the API key is valid and retrieve the associated user ID
function getUserIdByApiKey($pdo, $apiKey) {
    $query = "SELECT id FROM users WHERE api_key = :api_key";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['api_key' => $apiKey]);
    return $stmt->fetchColumn(); // Return user ID if valid, otherwise null
}

// Handle the form submission via POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $apiKey = $_POST['api_key'] ?? null; // Get the submitted API key
    $firstName = ucwords($_POST['first_name']) ?? null; // Get the first name
    $lastName = ucwords($_POST['last_name']) ?? null; // Get the last name
    $email = $_POST['email'] ?? null; // Get the email
    $password = $_POST['password'] ?? null; // Get the password

    // Validate input
    if (empty($apiKey) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Validate the API key and get the user ID
    $userId = getUserIdByApiKey($pdo, $apiKey);
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid API key.']);
        exit();
    } 

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the record into the app_user table
    $query = "INSERT INTO app_user (user_id, first_name, last_name, email, password) VALUES (:user_id, :first_name, :last_name, :email, :password)";
    $stmt = $pdo->prepare($query);
    
    try {
        $stmt->execute([
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        
        echo json_encode(['success' => true, 'message' => 'User added successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error adding user: ' . $e->getMessage()]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
