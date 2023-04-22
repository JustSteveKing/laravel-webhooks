<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Tests\Stubs;

use Illuminate\Notifications\Notification;
use JustSteveKing\Webhooks\Builder\PendingWebhook;
use JustSteveKing\Webhooks\Facades\Webhook;

final class TestNotification extends Notification
{
    public function toWebhook(mixed $notifiable): PendingWebhook
    {
        return Webhook::to(
            url: 'https://www.some-url.com/',
        )->with(
            payload: [],
        );
    }
}
