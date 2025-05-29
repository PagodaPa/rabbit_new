<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\RabbitMQPublisher;
use App\TaskDTO;

define('MILLISECONDS', 1000);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод должен быть POST');
    }

    // создание DTO
    $taskDTO = new TaskDTO($_POST);

    // Создаем экземпляр публикатора
    $publisher = new RabbitMQPublisher();

    // Конвертируем секунды в миллисекунды
    $delayMs = $taskDTO->delay * MILLISECONDS;

    // Публикуем задачу
    $publisher->publish(
        [
            'task_type' => $taskDTO->task_type,
            'data' => $taskDTO->data
        ],
        $delayMs
    );

    echo 'Задача успешно отправлена в очередь!';

} catch (\Exception $e) {
    http_response_code(400);
    echo 'Ошибка: ' . $e->getMessage();
}
