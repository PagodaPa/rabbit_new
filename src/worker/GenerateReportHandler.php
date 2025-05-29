<?php 

namespace App\worker;

class GenerateReportHandler implements TaskHandlerInterface
{
    /**
     * @param array $data
     * 
     * @return void
     */
    public function handle(array $data): void
    {
        // логика генерации отчета
        $message = sprintf(
            "[%s] Генерация отчета для данных: %s\n",
            date('Y-m-d H:i:s'),
            json_encode($data)
        );
        file_put_contents(__DIR__ . '/../../logs/worker.log', $message, FILE_APPEND);
    }
}
