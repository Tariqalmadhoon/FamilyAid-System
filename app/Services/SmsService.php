<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    public function __construct(private string $driver = 'log')
    {
    }

    public function send(string $phone, string $message): void
    {
        if ($this->driver === 'log') {
            Log::info('SMS to ' . $phone . ': ' . $message);
            return;
        }

        // Future: integrate other providers based on $this->driver
    }
}
