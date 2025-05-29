<?php

namespace App\worker;

use PhpAmqpLib\Channel\AMQPChannel;

class Worker
{
    private $channel;
    private $handlers = [];

    /**
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param string $taskType
     * @param TaskHandlerInterface $handler
     * 
     * @return void
     */
    public function addHandler(string $taskType, TaskHandlerInterface $handler): void
    {
        $this->handlers[$taskType] = $handler;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $callback = function ($msg) {
            $task = json_decode($msg->body, true);
            $type = $task['type'] ?? null;
            $data = $task['data'] ?? [];

            try {
                if (!isset($this->handlers[$type])) {
                    throw new \Exception("Неизвестный тип задачи: $type");
                }
                $this->handlers[$type]->handle($data);

                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            } catch (\Exception $e) {
                file_put_contents(__DIR__ . '/logs/worker.log',
                    "[" . date('Y-m-d H:i:s') . "] Ошибка обработки задачи: " . $e->getMessage() . "\n", FILE_APPEND);
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, true);
            }
        };

        $this->channel->basic_consume('task_queue', '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}