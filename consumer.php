<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare('notifications_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

// Max retries
define('MAX_RETRIES', 3);

$callback = function ($msg) use ($channel) {
    $data = json_decode($msg->body, true);
    $headers = $msg->has('application_headers') ? $msg->get('application_headers')->getNativeData() : [];
    $retryCount = isset($headers['x-retries']) ? (int)$headers['x-retries'] : 0;

    echo " [x] Received notification ID: {$data['notification_id']} (Attempt: " . ($retryCount + 1) . ")\n";

    $success = sendNotification($data);

    if ($success) {
        echo " [v] Notification sent successfully\n";
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    } else {
        if ($retryCount < MAX_RETRIES - 1) {
            echo " [!] Failed to send. Requeuing (retry " . ($retryCount + 1) . ")\n";

            // Republish message with incremented retry count
            $newMsg = new AMQPMessage($msg->body, [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => new \PhpAmqpLib\Wire\AMQPTable([
                    'x-retries' => $retryCount + 1
                ])
            ]);
            $channel->basic_publish($newMsg, '', 'notifications_queue');
        } else {
            echo " [x] Max retries reached. Discarding message.\n";
            
        }

    
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
};

$channel->basic_consume('notifications_queue', '', false, false, false, false, $callback);


while (count($channel->callbacks)) {
    $channel->wait();
}


function sendNotification($data)
{
    return rand(0, 1) === 1;
}