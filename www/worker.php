<?php

// Регистрируем функцию, которая сама будет искать файлы классов
spl_autoload_register(function ($class) {
    // Префикс библиотеки
    $prefix = 'PhpAmqpLib\\';
    // Базовая директория, где лежат файлы (с учетом твоей вложенности)
    $base_dir = __DIR__ . '/vendor/php-amqplib/php-amqplib-master/PhpAmqpLib/';

    // Проверяем, относится ли класс к этой библиотеке
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Получаем относительное имя класса (например, Connection\AMQPStreamConnection)
    $relative_class = substr($class, $len);

    // Заменяем обратные слеши на прямые и добавляем .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Если файл существует — подключаем его
    if (file_exists($file)) {
        require_once $file;
    }
});

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$host = 'rabbitmq'; // Имя сервиса из docker-compose
$port = 5672;
$user = 'guest';
$pass = 'guest';

try {
    $connection = new AMQPStreamConnection($host, $port, $user, $pass);
    $channel = $connection->channel();

    $channel->queue_declare('hello', false, false, false, false);

    echo " [*] Ожидание сообщений. Для выхода нажмите CTRL+C\n";

    $callback = function ($msg) {
        echo " [x] Получено сообщение: ", $msg->body, "\n";
        // Здесь могла бы быть логика обработки (запись в БД, отправка email и т.д.)
    };

    $channel->basic_consume('hello', '', false, true, false, false, $callback);

    while ($channel->is_consuming()) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();

} catch (Exception $e) {
    echo "Ошибка подключения: " . $e->getMessage() . "\n";
}
