<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare('notifications_queue', false, true, false, false);

$data = json_encode([
    'notification_id' => 1,
    'type' => 'email',
    'to' => 'user@example.com',
    'message' => 'Test notification from producer'
]);

$msg = new AMQPMessage($data, [
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
]);

$channel->basic_publish($msg, '', 'notifications_queue');

echo " [x] Sent notification to queue\n";

$channel->close();
$connection->close();