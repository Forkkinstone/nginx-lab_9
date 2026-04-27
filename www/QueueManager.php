<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager {
    private $connection;
    private $channel;
    private $queueName = 'lab7_queue';

    public function __construct() {
        // Подключаемся к сервису 'rabbitmq' (имя из docker-compose)
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    public function publish($data) {
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]); // delivery_mode 2 делает сообщение стойким
        $this->channel->basic_publish($msg, '', $this->queueName);
    }

    public function consume(callable $callback) {
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, function($msg) use ($callback) {
            $data = json_decode($msg->body, true);
            $callback($data);
        });

        while($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct() {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
