<?php

namespace App\Services\Whatsapp;

interface WhatsappAdapter
{
    /**
     * Send a text message via WhatsApp.
     * Returns ['ok'=>bool, 'msg_id'=>?string, 'error'=>?string]
     */
    public function sendText(string $toPhone, string $message): array;
}
