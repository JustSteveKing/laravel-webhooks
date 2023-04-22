<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Tests\Stubs;

use Illuminate\Notifications\Notifiable;

final class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForWebhook(): string
    {
        return 'https://some-url.com/';
    }
}
