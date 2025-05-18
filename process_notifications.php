<?php
require_once 'db.php';  // Your DB connection file

$sql = "SELECT n.id, n.user_id, n.type, n.message, u.email, u.phone 
        FROM notifications n
        JOIN users u ON n.user_id = u.id
        WHERE n.status = 'pending'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($notif = $result->fetch_assoc()) {
        $notif_id = $notif['id'];
        $type = $notif['type'];
        $message = $notif['message'];
        $email = $notif['email'];
        $phone = $notif['phone'];

        $sent = false;

        switch ($type) {
            case 'email':
                // Simulate email sending
                echo "Email sent to $email: $message\n";
                $sent = true;
                break;

            case 'sms':
                // Simulate SMS sending
                echo "SMS sent to $phone: $message\n";
                $sent = true;
                break;

            case 'in-app':
                // Just mark as sent
                echo "In-app notification for user {$notif['user_id']}: $message\n";
                $sent = true;
                break;

            default:
                echo "Unknown notification type: $type\n";
        }

        $status = $sent ? 'sent' : 'failed';
        $conn->query("UPDATE notifications SET status='$status' WHERE id=$notif_id");
    }
} else {
    echo "No pending notifications to process.\n";
}

$conn->close();
?>