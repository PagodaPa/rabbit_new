<?php

namespace App\worker;

interface TaskHandlerInterface
{
    /**
     * @param array $data
     * 
     * @return void
     */
    public function handle(array $data): void;
}

