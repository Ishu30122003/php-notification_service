<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "notification_service";



$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}


if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}



$user_id = intval($_GET['user_id']);


$user_check = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($user_check->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    
}
exit;


$sql = "SELECT id, type, message, status, created_at FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

echo json_encode(['notifications' => $notifications]);

$conn->close();
?>