<?php 

namespace App\worker;

class SendEmailHandler implements TaskHandlerInterface
{
    /**
     * @param array $data
     * 
     * @return void
     */
    public function handle(array $data): void
    {
        // логика отправки email
        $message = sprintf(
            "[%s] Отправка email на %s с сообщением: %s\n",
            date('Y-m-d H:i:s'),
            $data['email'] ?? 'unknown',
            $data['message'] ?? 'no message'
        );
        file_put_contents(__DIR__ . '/../../logs/worker.log', $message, FILE_APPEND);
    }
}