<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQPublisher
{
    private string $host;
    private string $port;
    private string $user;
    private string $pass;
    private string $exchange;
    private string $queue;
    private string $routingKey;

    public function __construct(
        string $host = 'rabbitmq',
        int $port = 5672,
        string $user = 'guest',
        string $pass = 'guest',
        string $exchange = 'delayed_exchange',
        string $queue = 'task_queue',
        string $routingKey = 'task_routing_key'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = $routingKey;
    }

    /**
     * @param array $taskData
     * @param int $delayMilliseconds
     * 
     * @return void
     */
    public function publish(array $taskData, int $delayMilliseconds): void
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass);
        $channel = $connection->channel();

        // Объявляем exchange с правильными аргументами
        $args = new AMQPTable(['x-delayed-type' => 'direct']);
        $channel->exchange_declare(
            $this->exchange,
            'x-delayed-message',
            false,  // passive
            true,   // durable
            false,  // auto_delete
            false,  // internal
            false,  // nowait
            $args   // arguments
        );

        // Объявляем очередь
        $channel->queue_declare($this->queue, false, true, false, false);

        // Связываем очередь с exchange
        $channel->queue_bind($this->queue, $this->exchange, $this->routingKey);

        $task = [
            'type' => $taskData['task_type'],
            'data' => $taskData['data'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $msg = new AMQPMessage(
            json_encode($task),
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => new AMQPTable(['x-delay' => $delayMilliseconds]),
            ]
        );

        $channel->basic_publish($msg, $this->exchange, $this->routingKey);
        $channel->close();
        $connection->close();
    }
}
