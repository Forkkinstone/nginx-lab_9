<?php

spl_autoload_register(function ($class) {
    $prefix = 'PhpAmqpLib\\';
    // Путь к папке, которую мы скачали с GitHub
    $base_dir = __DIR__ . '/vendor/php-amqplib/php-amqplib-master/PhpAmqpLib/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});


if (file_exists(__DIR__ . '/QueueManager.php')) {
    require_once __DIR__ . '/QueueManager.php';
}

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$statusMessage = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $userMessage = htmlspecialchars($_POST['message']);
    
    try {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage($userMessage);

        $channel->basic_publish($msg, '', 'hello');

        $statusMessage = "✅ Сообщение успешно отправлено в RabbitMQ: " . $userMessage;

        $channel->close();
        $connection->close();
    } catch (Exception $e) {
        $statusMessage = "❌ Ошибка при отправке: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>RabbitMQ Lab 7</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 500px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input[type="text"] { width: 80%; padding: 10px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; }
        .status { margin-top: 20px; padding: 10px; border-radius: 4px; background: #e9ecef; }
    </style>
</head>
<body>

<div class="container">
    <h2>Отправка сообщения в очередь</h2>
    <form method="POST">
        <input type="text" name="message" placeholder="Введите сообщение..." required>
        <button type="submit">Отправить</button>
    </form>

    <?php if ($statusMessage): ?>
        <div class="status">
            <?php echo $statusMessage; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
