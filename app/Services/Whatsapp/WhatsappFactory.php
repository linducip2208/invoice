<?php

namespace App\Services\Whatsapp;

use App\Models\Workspace;

class WhatsappFactory
{
    /**
     * Resolve adapter from workspace settings.
     * Workspace must have whatsapp_config JSON field with adapter params.
     */
    public static function resolve(?Workspace $workspace): ?WhatsappAdapter
    {
        $workspace = $workspace ?? workspace();
        if (! $workspace) return null;

        $config = $workspace->whatsapp_config ?? null;
        if (! $config || empty($config['endpoint'])) return null;

        return new GenericRestAdapter(
            endpoint: $config['endpoint'],
            authHeader: $config['auth_header'] ?? '',
            phoneField: $config['phone_field'] ?? 'target',
            messageField: $config['message_field'] ?? 'message',
            extraPayload: $config['extra_payload'] ?? [],
        );
    }
}
