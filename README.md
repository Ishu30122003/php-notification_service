# Notification Service

## Overview

This project implements a Notification Service that supports sending notifications via *Email, **SMS, and **In-App* channels. It uses PHP and MySQL for the backend and RabbitMQ for asynchronous processing with retry logic.

---

## Features

- Queue notifications via a REST API (send_notifications.php)
- Retrieve user notifications (get_users_notifications.php)
- Process pending notifications synchronously (process_notifications.php)
- Process notifications asynchronously using RabbitMQ (producer.php and consumer.php)
- Retry failed notifications up to 3 times
- Notification types supported: *email, **sms, **in-app*

---

## Setup Instructions

### Prerequisites

- PHP 7.4+ with mysqli extension(tested on PHP 8.0.30)
- MySQL or MariaDB
- Composer (PHP dependency manager)
- RabbitMQ server running locally (default host: localhost, port: 5672)
- Web server or PHP built-in server

