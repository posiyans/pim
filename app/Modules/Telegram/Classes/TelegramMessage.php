<?php

namespace App\Modules\Telegram\Classes;


use Illuminate\Support\Facades\Log;

class TelegramMessage
{
    private array $payload;


    public function __construct()
    {
        $this->payload = [
            'text' => '',
            'chat_id' => ''
        ];
    }

    public function to(int|string $chatId): self
    {
        $this->payload['chat_id'] = $chatId;
        return $this;
    }

    public function line(string $content): self
    {
        $this->payload['text'] .= $content . "\n";
        return $this;
    }

    public function hasToken(): bool
    {
        return false;
    }

    public function toNotGiven(): bool
    {
        return false;
//        return !isset($this->payload['chat_id']);
    }

    public function send()
    {
        try {
            $telegram = new TelegramClass();
            $telegram->sendMessage($this->payload);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
