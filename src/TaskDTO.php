<?php

namespace App;

use InvalidArgumentException;

class TaskDTO
{
    public string $task_type;
    public int $delay;
    public array $data;

    /**
     * Конструктор принимает "сырые" данные (например, из $_POST)
     * Используем строгую типизацию и базовую фильтрацию
     *
     * @param array $input данные из формы
     * @throws InvalidArgumentException если данные невалидны
     */
    public function __construct(array $input)
    {
        // task_type: ожидаем non-empty строку из списка допустимых
        if (empty($input['task_type']) || !in_array($input['task_type'], ['send_email', 'generate_report'])) {
            throw new InvalidArgumentException('Неверный тип задачи');
        }
        $this->task_type = $input['task_type'];

        // delay: целое число от 0 и выше
        $delay = filter_var($input['delay'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($delay === false) {
            throw new InvalidArgumentException('Задержка должна быть целым числом >= 0');
        }
        $this->delay = $delay;

        // data: JSON, который дожен декодироваться в массив
        $data_raw = $input['data'] ?? '';
        $dataDecoded = json_decode($data_raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($dataDecoded)) {
            throw new InvalidArgumentException('Данные задачи должны быть валидным JSON объектом');
        }
        $this->data = $dataDecoded;
    }
}
