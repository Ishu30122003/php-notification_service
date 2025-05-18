<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";  // default for XAMPP
$password = "";
$dbname = "notification_service";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['type']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$user_id = intval($data['user_id']);
$type = strtolower($data['type']);
$message = $conn->real_escape_string($data['message']);

$valid_types = ['email', 'sms', 'in-app'];
if (!in_array($type, $valid_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid notification type']);
    exit;
}


$user_check = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($user_check->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}


$sql = "INSERT INTO notifications (user_id, type, message) VALUES ($user_id, '$type', '$message')";
if ($conn->query($sql) === TRUE) {
    http_response_code(201);
    echo json_encode(['message' => 'Notification queued']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to insert notification']);
}

$conn->close();
?>